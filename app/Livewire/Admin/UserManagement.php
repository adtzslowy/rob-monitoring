<?php

namespace App\Livewire\Admin;

use App\Models\DashSetting;
use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Title("Manajemen User")]
class UserManagement extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';
    public int $perPage = 10;

    public bool $modalOpen = false;
    public string $mode = 'create';
    public ?int $editId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'operator';

    /** @var array<int> */
    public array $operator_device_ids = [];

    /** @var array<int, string> */
    public array $roles = [];

    public function mount(): void
    {
        $this->roles = Role::query()->pluck('name')->values()->all();

        if (empty($this->roles)) {
            $this->roles = ['admin', 'operator'];
        }

        $this->role = $this->roles[0] ?? 'operator';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();

        $this->role = 'operator';
        $this->operator_device_ids = [];

        $this->editId = null;
        $this->mode = 'create';
        $this->modalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $u = User::with('dashSetting', 'roles', 'devices')->findOrFail($id);

        $this->resetForm();
        $this->mode = 'edit';
        $this->editId = $u->id;

        $this->name = (string) $u->name;
        $this->email = (string) $u->email;

        $this->role = $u->roles->first()?->name ?? ($this->roles[0] ?? 'operator');

        // ✅ many-to-many: device ids
        $this->operator_device_ids = $u->devices()
            ->pluck('devices.id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $this->modalOpen = true;
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
    }

    public function save(): void
    {
        $isEdit = $this->mode === 'edit';

        // ✅ normalize ints
        $this->operator_device_ids = array_values(array_unique(array_map('intval', $this->operator_device_ids ?? [])));

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                $isEdit
                    ? Rule::unique('users', 'email')->ignore($this->editId)
                    : Rule::unique('users', 'email'),
            ],
            'role' => ['required', 'string'],
        ];

        if ($this->role === 'operator') {
            $rules['operator_device_ids'] = ['required', 'array', 'min:1'];
            $rules['operator_device_ids.*'] = ['integer', 'exists:devices,id'];
        } else {
            $rules['operator_device_ids'] = ['nullable', 'array'];
        }

        if ($isEdit) {
            if (!empty($this->password)) {
                $rules['password'] = ['min:6', 'max:255'];
            }
        } else {
            $rules['password'] = ['required', 'min:6', 'max:255'];
        }

        $validated = $this->validate($rules);

        if (!in_array($validated['role'], $this->roles, true)) {
            $this->addError('role', 'Role tidak valid.');
            return;
        }

        // =========================
        // CREATE / UPDATE USER
        // =========================
        if ($isEdit) {
            $u = User::query()->findOrFail($this->editId);

            $u->name = $validated['name'];
            $u->email = $validated['email'];

            if (!empty($this->password)) {
                $u->password = Hash::make($this->password);
            }

            $u->save();
        } else {
            $u = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
        }

        // =========================
        // ROLE (Spatie)
        // =========================
        $u->syncRoles([$validated['role']]);

        // =========================
        // DASHBOARD SETTING
        // =========================
        $setting = DashSetting::firstOrCreate(
            ['user_id' => $u->id],
            ['theme' => 'dark', 'visible_sensors' => json_encode([])]
        );

        if ($validated['role'] === 'operator') {
            $ids = array_values(array_unique(array_map('intval', $validated['operator_device_ids'] ?? [])));

            // ✅ backend guard: block devices already owned by other operators
            $conflicts = Device::query()
                ->whereIn('id', $ids)
                ->whereHas('users', function ($uq) {
                    // users yang role operator
                    $uq->whereHas('roles', function ($rq) {
                        $rq->where('name', 'operator');
                    });

                    // exclude diri sendiri ketika edit
                    if ($this->editId) {
                        $uq->where('users.id', '!=', $this->editId);
                    }
                })
                ->pluck('id')
                ->toArray();

            if (!empty($conflicts)) {
                $this->addError('operator_device_ids', 'Ada alat yang sudah dimiliki operator lain. Pilih alat lain.');
                return;
            }

            // ✅ sync pivot
            $u->devices()->sync($ids);

            // ✅ keep selected_device_id valid
            $current = $setting->selected_device_id ? (int) $setting->selected_device_id : null;

            $setting->update([
                'selected_device_id' => ($current && in_array($current, $ids, true))
                    ? $current
                    : ($ids[0] ?? null),
            ]);
        } else {
            $u->devices()->sync([]);
            $setting->update(['selected_device_id' => null]);
        }

        session()->flash('success', $isEdit ? 'User berhasil diperbarui.' : 'User berhasil ditambahkan.');

        $this->closeModal();
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Tidak bisa menghapus akun sendiri.');
            return;
        }

        $u = User::query()->findOrFail($id);
        $u->delete();

        session()->flash('success', 'User berhasil dihapus.');

        if ($this->perPage > 1 && $this->getUsersQuery()->paginate($this->perPage)->count() === 0) {
            $this->previousPage();
        }
    }

    private function resetForm(): void
    {
        $this->resetValidation();

        $this->editId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = $this->roles[0] ?? 'operator';
        $this->operator_device_ids = [];
    }

    private function getUsersQuery()
    {
        return User::query()
            ->with(['roles', 'devices'])
            ->when($this->search, function ($q) {
                $s = trim($this->search);

                $q->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%");
                });
            })
            ->latest();
    }

    public function render()
    {
        $users = $this->getUsersQuery()->paginate($this->perPage);

        // ✅ Filter device:
        // - CREATE: hanya device yang belum dimiliki operator manapun
        // - EDIT: tambah device milik user yang sedang diedit (biar tetap muncul)
        $devicesQuery = Device::query()->orderBy('name');

        if ($this->editId) {
            $devicesQuery->where(function ($q) {
                $q->whereDoesntHave('users', function ($uq) {
                    $uq->whereHas('roles', fn ($rq) => $rq->where('name', 'operator'));
                });

                $q->orWhereHas('users', function ($uq) {
                    $uq->where('users.id', $this->editId);
                });
            });
        } else {
            $devicesQuery->whereDoesntHave('users', function ($uq) {
                $uq->whereHas('roles', fn ($rq) => $rq->where('name', 'operator'));
            });
        }

        $devices = $devicesQuery
            ->get(['id', 'name'])
            ->map(fn ($d) => [
                'value' => (int) $d->id,
                'label' => (string) ($d->name ?? ('ROB ' . $d->id)),
            ])
            ->toArray();

        return view('livewire.admin.user-management', [
            'users' => $users,
            'devices' => $devices,
        ]);
    }
}

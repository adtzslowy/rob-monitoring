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
    public ?int $operator_device_id = null;

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
        $this->operator_device_id = null;

        $this->editId = null; // ✅ jangan false
        $this->mode = 'create';
        $this->modalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $u = User::with('dashSetting', 'roles')->findOrFail($id);

        $this->resetForm();
        $this->mode = 'edit';
        $this->editId = $u->id;

        $this->name = (string) $u->name;
        $this->email = (string) $u->email;

        $this->role = $u->roles->first()?->name ?? ($this->roles[0] ?? 'operator');

        // ✅ isi device operator dari dashboard_setting
        $this->operator_device_id = $u->dashSetting?->selected_device_id;

        $this->modalOpen = true;
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
    }

    public function save(): void
    {
        $isEdit = $this->mode === 'edit';

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

        // ✅ operator wajib pilih device
        if ($this->role === 'operator') {
            $rules['operator_device_id'] = ['required', 'integer', 'exists:devices,id'];
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
        // DASHBOARD SETTING (create & edit)
        // =========================
        $setting = DashSetting::firstOrCreate(
            ['user_id' => $u->id],
            [
                'theme' => 'dark',
                'visible_sensors' => json_encode([]),
            ]
        );

        if ($validated['role'] === 'operator') {
            $setting->update([
                'selected_device_id' => $this->operator_device_id,
            ]);
        } else {
            $setting->update([
                'selected_device_id' => null,
            ]);
        }

        session()->flash('message', $isEdit ? 'User berhasil diperbarui.' : 'User berhasil ditambahkan.');

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

        session()->flash('message', 'User berhasil dihapus.');

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
        $this->operator_device_id = null; // ✅ penting
    }

    private function getUsersQuery()
    {
        return User::query()
            ->with('roles')
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

        $devices = Device::orderBy('name')->get(['id', 'name']);

        return view('livewire.admin.user-management', [
            'users' => $users,
            'devices' => $devices,
        ]);
    }
}

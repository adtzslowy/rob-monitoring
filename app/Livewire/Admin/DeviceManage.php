<?php

namespace App\Livewire\Admin;

use App\Models\Device;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title("Manajemen Device")]
class DeviceManage extends Component
{
    use WithPagination;

    // ✅ RBAC
    public bool $canManageDevices = false;

    // list state
    public string $search = '';
    public int $perPage = 10;

    // modal state
    public bool $modalOpen = false;
    public ?int $editId = null;

    // form fields
    public string $name = '';
    public ?string $alias = null;
    public ?float $latitude = null;
    public ?float $longitude = null;

    // status dari alat (string)
    public string $status = 'offline'; // online|offline

    // last_seen: buat input type="datetime-local"
    public ?string $last_seen = null;

    public function mount(): void
    {
        $user = auth()->user();

        // ✅ pakai permission kalau kamu sudah pakai permission "manage devices"
        // fallback ke role admin kalau permission belum ada
        $this->canManageDevices = (bool) (
            ($user?->can('manage devices') ?? false)
            || ($user?->hasRole('admin') ?? false)
        );
    }

    private function assertAdmin(): void
    {
        abort_unless($this->canManageDevices, 403, 'Only admin can manage devices');
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->assertAdmin();

        $this->resetForm();
        $this->modalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $this->assertAdmin();

        $d = Device::query()->findOrFail($id);

        $this->editId = (int) $d->id;
        $this->name = (string) ($d->name ?? '');
        $this->alias = $d->alias;
        $this->latitude = $d->latitude !== null ? (float) $d->latitude : null;
        $this->longitude = $d->longitude !== null ? (float) $d->longitude : null;
        $this->status = $d->status ?: 'offline';

        // datetime-local format: Y-m-d\TH:i (anggap data di DB adalah Asia/Jakarta)
        $this->last_seen = $d->last_seen
            ? Carbon::parse($d->last_seen, 'UTC')
                ->setTimezone('Asia/Jakarta')
                ->format('Y-m-d\TH:i')
            : null;

        $this->modalOpen = true;
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->editId = null;

        $this->name = '';
        $this->alias = null;
        $this->latitude = null;
        $this->longitude = null;
        $this->status = 'offline';
        $this->last_seen = null;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'alias' => ['nullable','string','max:255'],
            'latitude' => ['nullable','numeric','between:-90,90'],
            'longitude' => ['nullable','numeric','between:-180,180'],
            'status' => ['required','in:online,offline'],
            'last_seen' => ['nullable','date'],
        ];
    }

    public function save(): void
    {
        $this->assertAdmin();

        $this->validate();

        $payload = [
            'name' => $this->name,
            'alias' => $this->alias,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'last_seen' => null,
        ];

        if ($this->last_seen) {
            $payload['last_seen'] = Carbon::createFromFormat('Y-m-d\TH:i', $this->last_seen, 'Asia/Jakarta')
                ->setTimezone('UTC')
                ->format('Y-m-d H:i:s');
        }

        Device::query()->updateOrCreate(
            ['id' => $this->editId],
            $payload
        );

        session()->flash('success', $this->editId ? 'Alat berhasil diupdate.' : 'Alat berhasil ditambahkan.');
        $this->closeModal();
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $this->assertAdmin();

        Device::query()->whereKey($id)->delete();
        session()->flash('success', 'Alat berhasil dihapus.');

        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        abort_unless($user, 403);

        /**
         * ✅ Admin -> semua device
         * ✅ Operator -> device yang ada di pivot device_user (yang dipilih admin)
         */
        $q = $this->canManageDevices
            ? Device::query()
            : $user->devices(); // belongsToMany

        // penting: qualify kolom pakai "devices." biar aman saat join pivot
        $q->select([
            'devices.id',
            'devices.name',
            'devices.alias',
            'devices.status',
            'devices.last_seen',
            'devices.latitude',
            'devices.longitude',
        ]);

        if ($this->search !== '') {
            $s = trim($this->search);

            $q->where(function ($qq) use ($s) {
                $qq->where('devices.name', 'like', "%{$s}%")
                   ->orWhere('devices.alias', 'like', "%{$s}%");

                if (ctype_digit($s)) {
                    $qq->orWhere('devices.id', (int) $s);
                }
            });
        }

        $devices = $q->orderByDesc('devices.id')->paginate($this->perPage);

        return view('livewire.admin.device-manage', compact('devices'));
    }
}

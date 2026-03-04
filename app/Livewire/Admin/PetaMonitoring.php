<?php

namespace App\Livewire\Admin;

use App\Models\Device;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Peta Monitoring')]
class PetaMonitoring extends Component
{
    public bool $canManageDevices = false;
    public string $windyKey = '';
    public array $devices = [];

    public function mount(): void
    {
        $user = auth()->user();

        $this->canManageDevices = (bool) (
            ($user?->can('manage devices') ?? false) ||
            ($user?->hasRole('admin') ?? false)
        );

        // ✅ WINDY API KEY (bukan url)
        $this->windyKey = (string) config('services.windy.key', env('WINDY_API_KEY', ''));

        $this->loadDevices();
    }

    public function loadDevices(): void
    {
        $user = auth()->user();

        $q = $this->canManageDevices
            ? Device::query()
            : $user->devices(); // belongsToMany pivot device_user

        $rows = $q->select([
                'devices.id',
                'devices.name',
                'devices.alias',
                'devices.latitude',
                'devices.longitude',
                'devices.status',
                'devices.last_seen',
            ])
            ->whereNotNull('devices.latitude')
            ->whereNotNull('devices.longitude')
            ->orderBy('devices.id')
            ->get();

        // ✅ kirim lat/lng supaya cocok dengan JS
        $this->devices = $rows->map(fn ($d) => [
            'id' => (int) $d->id,
            'name' => $d->name ?? ('ROB ' . $d->id),
            'alias' => $d->alias,
            'lat' => (float) $d->latitude,
            'lng' => (float) $d->longitude,
            'status' => strtolower((string) ($d->status ?? 'offline')),
            'last_seen' => $d->last_seen
                ? Carbon::parse($d->last_seen)->timezone('Asia/Jakarta')->format('d M H:i')
                : '-',
        ])->toArray();

        // ✅ event name harus sama dengan listener Blade: render-markers
        $this->dispatch('render-markers', devices: $this->devices);
    }

    public function render()
    {
        return view('livewire.admin.peta-monitoring');
    }
}

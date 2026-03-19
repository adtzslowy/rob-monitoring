<?php

namespace App\Livewire\Admin;

use App\Models\Device;
use App\Models\SensorReading;
use App\Services\FuzzyRiskServices;
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

        $this->windyKey = (string) config('services.windy.key', env('WINDY_API_KEY', ''));

        $this->loadDevices();
    }

    public function loadDevices(): void
    {
        $user  = auth()->user();
        $fuzzy = app(FuzzyRiskServices::class);

        $q = $this->canManageDevices
            ? Device::query()
            : $user->devices();

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

        $this->devices = $rows->map(function ($d) use ($fuzzy) {
            $reading = SensorReading::where('device_id', $d->id)
                ->latest('timestamp')
                ->first();

            $risk = $reading
                ? $fuzzy->evaluate([
                    'ketinggian_air'  => $reading->ketinggian_air,
                    'tekanan_udara'   => $reading->tekanan_udara,
                    'kecepatan_angin' => $reading->kecepatan_angin,
                    'arah_angin'      => $reading->arah_angin,
                ])
                : ['score' => 0, 'label' => 'UNKNOWN'];

            return [
                'id'            => (int) $d->id,
                'name'          => $d->name ?? ('ROB ' . $d->id),
                'alias'         => $d->alias,
                'lat'           => (float) $d->latitude,
                'lng'           => (float) $d->longitude,
                'status'        => strtolower((string) ($d->status ?? 'offline')),
                'last_seen'     => $d->last_seen
                    ? Carbon::parse($d->last_seen)->setTimezone('Asia/Jakarta')->format('d M H:i')
                    : '-',
                'status_risiko' => $risk['label'],
                'score'         => $risk['score'],
                'sensor'        => $reading ? [
                    'ketinggian_air'  => $reading->ketinggian_air,
                    'suhu'            => $reading->suhu,
                    'kelembapan'      => $reading->kelembapan,
                    'tekanan_udara'   => $reading->tekanan_udara,
                    'kecepatan_angin' => $reading->kecepatan_angin,
                    'arah_angin'      => $reading->arah_angin,
                    'timestamp'       => $reading->timestamp
                        ? Carbon::parse($reading->timestamp)->setTimezone('Asia/Jakarta')->format('d M H:i:s')
                        : '-',
                ] : null,
            ];
        })->toArray();

        $this->dispatch('render-markers', devices: $this->devices);
    }

    public function render()
    {
        return view('livewire.admin.peta-monitoring');
    }
}
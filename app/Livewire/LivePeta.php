<?php

namespace App\Livewire;

use App\Models\Device;
use App\Models\SensorReading;
use App\Services\FuzzyRiskServices;
use Carbon\Carbon;
use Livewire\Component;

class LivePeta extends Component
{
    public string $windyKey = '';
    public array $devices   = [];

    public function mount(): void
    {
        $this->windyKey = (string) config('services.windy.key', '');
        $this->loadDevices();
    }

    public function loadDevices(): void
    {
        $fuzzy = app(FuzzyRiskServices::class);

        $this->devices = Device::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('id')
            ->get()
            ->map(function ($d) use ($fuzzy) {
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
                    'status'        => strtolower($d->status ?? 'offline'),
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
                            ? Carbon::parse($reading->timestamp)
                                ->setTimezone('Asia/Jakarta')
                                ->format('d M Y H:i:s')
                            : '-',
                    ] : null,
                ];
            })
            ->toArray();

        $this->dispatch('render-markers', devices: $this->devices);
    }

    public function render()
    {
        return view('livewire.live-peta');
    }
}

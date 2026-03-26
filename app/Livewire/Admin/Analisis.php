<?php

namespace App\Livewire\Admin;

use App\Models\Device;
use App\Models\SensorReading;
use App\Services\BmkgServices;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Analisis Cuaca')]
class Analisis extends Component
{
    public string $selectedWilayah = 'delta_pawan';
    public string $selectedDevice  = '';

    public array $bmkgData   = [];
    public array $sensorData = [];
    public array $devices    = [];
    public array $comparison = [];

    public function mount(): void
    {
        $this->devices = Device::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($d) {
                $latest = SensorReading::where('device_id', $d->id)
                    ->latest('timestamp')
                    ->first();

                return [
                    'id'    => $d->id,
                    'label' => $d->alias ?? $d->name,
                ];
            })
            ->toArray();

        if (!empty($this->devices)) {
            $this->selectedDevice = (string) $this->devices[0]['id'];
        }

        $this->loadData();
    }

    public function getWilayahLabelProperty(): string
    {
        return BmkgServices::WILAYAH[$this->selectedWilayah]['label'] ?? '';
    }

    public function getWilayahListProperty(): array
    {
        return BmkgServices::WILAYAH ?? [];
    }

    public function updatedSelectedWilayah(): void
    {
        $this->loadData();
    }

    public function updatedSelectedDevice(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $bmkg = app(BmkgServices::class);

        $wilayah        = $bmkg->getByWilayah($this->selectedWilayah);
        $this->bmkgData = $wilayah['prakiraan'] ?? [];

        if ($this->selectedDevice) {
            $this->sensorData = SensorReading::where('device_id', $this->selectedDevice)
                ->where('timestamp', '>=', now()->subHours(24))
                ->orderBy('timestamp')
                ->get()
                ->map(fn($r) => [
                    'local_datetime'   => Carbon::parse($r->timestamp)
                        ->setTimezone('Asia/Pontianak')
                        ->format('Y-m-d H:i:s'),
                    'suhu'             => $r->suhu,
                    'kelembapan'       => $r->kelembapan,
                    'tekanan_udara'    => $r->tekanan_udara,
                    'kecepatan_angin'  => $r->kecepatan_angin,
                    'arah_angin_deg'   => $r->arah_angin,
                    'arah_angin_label' => $r->arah_angin !== null
                        ? $this->degreesToCompass((float) $r->arah_angin)
                        : null,
                    'ketinggian_air'   => $r->ketinggian_air,
                ])
                ->toArray();
        }

        $this->comparison = $this->buildComparison();
        $this->dispatch('chartDataUpdated');
    }

    private function buildComparison(): array
    {
        $now     = Carbon::now('Asia/Pontianak');
        $closest = null;
        $minDiff = PHP_INT_MAX;

        foreach ($this->bmkgData as $item) {
            $dt   = Carbon::parse($item['local_datetime'], 'Asia/Pontianak');
            $diff = abs($now->diffInMinutes($dt));
            if ($diff < $minDiff) {
                $minDiff = $diff;
                $closest = $item;
            }
        }

        if (!$this->selectedDevice) {
            return ['bmkg' => $closest, 'sensor' => null, 'selisih' => null];
        }

        $recent = SensorReading::where('device_id', $this->selectedDevice)
            ->where('timestamp', '>=', now()->subHour())
            ->get();

        $sensorAvg = $recent->isNotEmpty() ? [
            'suhu'            => round($recent->avg('suhu'), 1),
            'kelembapan'      => round($recent->avg('kelembapan'), 1),
            'kecepatan_angin' => round($recent->avg('kecepatan_angin'), 1),
            'arah_angin_deg'  => round($recent->avg('arah_angin'), 1),
        ] : null;

        return [
            'bmkg'    => $closest,
            'sensor'  => $sensorAvg,
            'selisih' => $sensorAvg && $closest ? [
                'suhu'            => round(abs(($sensorAvg['suhu'] ?? 0) - ($closest['suhu'] ?? 0)), 1),
                'kelembapan'      => round(abs(($sensorAvg['kelembapan'] ?? 0) - ($closest['kelembapan'] ?? 0)), 1),
                'kecepatan_angin' => round(abs(($sensorAvg['kecepatan_angin'] ?? 0) - ($closest['kecepatan_angin'] ?? 0)), 1),
            ] : null,
        ];
    }

    private function degreesToCompass(float $deg): string
    {
        $directions = ['U', 'TL', 'T', 'TG', 'S', 'BD', 'B', 'BL'];
        $index = (int) round($deg / 45) % 8;
        return $directions[$index];
    }

    public function refreshBmkg(): void
    {
        app(BmkgServices::class)->clearCache();
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.analisis');
    }
}
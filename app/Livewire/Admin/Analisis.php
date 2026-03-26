<?php

namespace App\Livewire\Admin;

use App\Models\Device;
use App\Models\SensorReading;
use App\Services\BmkgServices;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Analisis Cuaca')]
class Analisis extends Component
{
    use WithPagination;

    public string $selectedWilayah = 'delta_pawan';
    public string $selectedDevice  = '';
    public int    $perPage         = 5;

    public array $bmkgData   = [];
    public array $devices    = [];
    public array $comparison = [];

    public function mount(): void
    {
        $this->devices = Device::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('id')
            ->get(['id', 'name', 'alias'])
            ->map(fn($d) => [
                'id'    => $d->id,
                'label' => $d->alias ?? $d->name ?? ('Device ' . $d->id),
            ])
            ->toArray();

        if (!empty($this->devices)) {
            $this->selectedDevice = (string) $this->devices[0]['id'];
        }

        $this->loadBmkg();
        $this->buildComparison();
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
        $this->resetPage('sensorPage');
        $this->loadBmkg();
        $this->buildComparison();
    }

    public function updatedSelectedDevice(): void
    {
        $this->resetPage('sensorPage');
        $this->buildComparison();
        $this->dispatch('chartDataUpdated');
    }

    public function updatedPerPage(): void
    {
        $this->resetPage('sensorPage');
    }

    public function loadBmkg(): void
    {
        $bmkg           = app(BmkgServices::class);
        $wilayah        = $bmkg->getByWilayah($this->selectedWilayah);
        $this->bmkgData = $wilayah['prakiraan'] ?? [];
    }

    public function buildComparison(): void
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
            $this->comparison = ['bmkg' => $closest, 'sensor' => null, 'selisih' => null];
            return;
        }

        // Pakai selectRaw AVG agar tidak load semua baris ke PHP
        $recent = SensorReading::where('device_id', $this->selectedDevice)
            ->where('timestamp', '>=', now()->subHour())
            ->selectRaw('
                AVG(suhu) as suhu,
                AVG(kelembapan) as kelembapan,
                AVG(kecepatan_angin) as kecepatan_angin,
                AVG(arah_angin) as arah_angin
            ')
            ->first();

        $sensorAvg = $recent && $recent->suhu !== null ? [
            'suhu'            => round($recent->suhu, 1),
            'kelembapan'      => round($recent->kelembapan, 1),
            'kecepatan_angin' => round($recent->kecepatan_angin, 1),
            'arah_angin_deg'  => round($recent->arah_angin, 1),
        ] : null;

        $this->comparison = [
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
        $index      = (int) round($deg / 45) % 8;
        return $directions[$index];
    }

    public function refreshBmkg(): void
    {
        app(BmkgServices::class)->clearCache();
        $this->loadBmkg();
        $this->buildComparison();
    }

    public function render()
    {
        $sensorData = null;

        if ($this->selectedDevice) {
            $sensorData = SensorReading::where('device_id', $this->selectedDevice)
                ->where('timestamp', '>=', now()->subHours(24))
                ->orderByDesc('timestamp')
                ->paginate($this->perPage, ['*'], 'sensorPage')
                ->through(fn($r) => [
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
                ]);
        }

        return view('livewire.admin.analisis', [
            'sensorData' => $sensorData,
        ]);
    }
}
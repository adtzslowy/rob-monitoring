<?php

namespace App\Livewire\Admin;

use App\Models\Device;
use App\Models\SensorReading;
use App\Services\BmkgServices;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Analisis extends Component
{

    public string $selectedWilayah = 'delta_pawan';
    public string $selectedDevice = '';

    public array $bmkgData = [];
    public array $sensorData = [];
    public array $devices = [];
    public array $comparison = [];
    public bool $loading = false;

    public function mount()
    {
        $this->devices = Device::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get()
                ->map(fn($d) => [
                    'id' => $d->id,
                    'label' => $d->alias ?? $d->name,
                ])
                ->toArray();
            
        if (!empty($this->devices)) {
            $this->selectedDevice = (string) $this->devices[0]['id'];
        }

        $this->loadData();
    }

    public function updatedSelectedWilayah(): void
    {
        $this->loadData();
    }

    public function updatedSelectedDevice(): void
    {
        $this->loadData();
    }

    public function loadData()
    {
        $bmkg = app(BmkgServices::class);

        $wilayah = $bmkg->getByWilayah($this->selectedWilayah);
        $this->bmkgData = $wilayah['prakiraan'] ?? [];


        if ($this->selectedDevice) {
            $readings = SensorReading::where('device_id', $this->selectedDevice)
                ->where('timestamp', '>=', now()->subHours(24))
                ->orderBy('timestamp')
                ->get()
                ->map(fn($r) => [
                    'local_datetime'  => Carbon::parse($r->timestamp)
                        ->setTimezone('Asia/Pontianak')
                        ->format('Y-m-d H:i:s'),
                    'suhu'            => $r->suhu,
                    'kelembapan'      => $r->kelembapan,
                    'kecepatan_angin' => $r->kecepatan_angin,
                    'arah_angin_deg'  => $r->arah_angin,
                    'tekanan_udara'   => $r->tekanan_udara,
                ])
                ->toArray();

            $this->sensorData = $readings;
        }

        $this->comparison = $this->buildComparison();
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

        // Rata-rata sensor 1 jam terakhir
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
            'bmkg'       => $closest,
            'sensor'     => $sensorAvg,
            'selisih'    => $sensorAvg && $closest ? [
                'suhu'            => round(abs($sensorAvg['suhu'] - $closest['suhu']), 1),
                'kelembapan'      => round(abs($sensorAvg['kelembapan'] - $closest['kelembapan']), 1),
                'kecepatan_angin' => round(abs($sensorAvg['kecepatan_angin'] - $closest['kecepatan_angin']), 1),
            ] : null,
        ];
    }

    public function refreshBmkg()
    {
        app(BmkgServices::class)->clearCache();
        $this->loadData();
        $this->dispatch('notify', message: 'Data BMKG diperbarui!');
    }

    public function render()
    {
        return view('livewire.admin.analisis');
    }
}

<?php

namespace App\Livewire;

use App\Models\Device;
use App\Services\WeatherAnalisisService;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Analisis - ROB Monitoring')]
class Analisis extends Component
{
    public array $devices = [];

    public int $selectedDeviceId = 0;

    public array $analisisData = [];

    public array $sensorReadings = [];

    public function mount(): void
    {
        $this->loadDevices();
    }

    private function loadDevices(): void
    {
        $this->devices = Device::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'name', 'alias'])
            ->map(fn ($d) => [
                'id' => $d->id,
                'label' => $d->alias ?? $d->name ?? 'Device '.$d->id,
            ])
            ->toArray();

        if (! empty($this->devices) && $this->selectedDeviceId === 0) {
            $this->selectedDeviceId = $this->devices[0]['id'];
        }

        $this->runAnalisis();
    }

    public function updatedSelectedDeviceId(): void
    {
        $this->runAnalisis();
    }

    public function refresh(): void
    {
        $this->runAnalisis();
    }

    private function runAnalisis(): void
    {
        if ($this->selectedDeviceId === 0) {
            $this->analisisData = [];
            $this->sensorReadings = [];

            return;
        }

        $service = app(WeatherAnalisisService::class);
        $this->analisisData = $service->analyze($this->selectedDeviceId, 'delta_pawan');
        $this->sensorReadings = $service->getLatestSensorData($this->selectedDeviceId);
    }

    public function render()
    {
        return view('livewire.analisis');
    }
}

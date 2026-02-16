<?php

namespace App\Livewire\Admin;

use App\Models\PetaMonitoring as ModelsPetaMonitoring;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Peta Monitoring')]
class PetaMonitoring extends Component
{
    public $locations = [];
    public $name;
    public $latitude;
    public $longitude;

    protected $listeners = ['setCoordinates'];

    public function mount()
    {
        $this->loadLocations();
    }

    public function loadLocations()
    {
        $this->locations = ModelsPetaMonitoring::all()->toArray();

        $this->dispatch('renderMarkers', locations: $this->locations);
    }

    public function setCoordinates($lat, $lng)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
    }

    public function saveLocation()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        ModelsPetaMonitoring::create([
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        $this->reset(['name', 'latitude', 'longitude']);

        $this->loadLocations();
    }

    public function render()
    {
        return view('livewire.admin.peta-monitoring');
    }
}

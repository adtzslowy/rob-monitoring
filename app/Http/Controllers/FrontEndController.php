<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\WeatherAnalisisService;

class FrontEndController extends Controller
{
    public function beranda()
    {
        return view('landing.home');
    }

    public function about()
    {
        return view('landing.tentang');
    }

    public function maps()
    {
        return view('landing.peta-alat');
    }

    public function analisis()
    {
        $devices = Device::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'name', 'alias', 'latitude', 'longitude']);

        $selectedDeviceId = request()->query('device', $devices->first()?->id);
        $selectedDevice = $devices->find($selectedDeviceId);

        $analisisData = [];
        $sensorReadings = [];

        if ($selectedDevice) {
            $service = app(WeatherAnalisisService::class);
            $analisisData = $service->analyze($selectedDevice->id, 'delta_pawan');
            $sensorReadings = $service->getLatestSensorData($selectedDevice->id);
        }

        return view('landing.analisis', [
            'devices' => $devices,
            'selectedDeviceId' => $selectedDeviceId,
            'analisisData' => $analisisData,
            'sensorReadings' => $sensorReadings,
        ]);
    }

    public function contact()
    {
        return view('landing.contact');
    }
}

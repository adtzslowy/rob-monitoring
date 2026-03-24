<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BmkgServices
{

    const WILAYAH = [
        'delta_pawan'   => ['adm4' => '61.04.16.1001', 'label' => 'Delta Pawan'],
        'sungai_awan'   => ['adm4' => '61.04.15.1001', 'label' => 'Sungai Awan'],
        'benua_kayong'  => ['adm4' => '61.04.02.1001', 'label' => 'Benua Kayong'],
    ];

    private function fetch(string $adm4, string $label)
    {
        try {
            $response = Http::timeout(10)
                ->get('https://api.bmkg.go.id/publik/prakiraan-cuaca', [
                    'adm4' => $adm4,
                ]);

            if (!$response->ok()) {
                return ['label' => $label, 'prakiraan' => [], 'error' => 'HTTP ' . $response->status()];
            }

            $json = $response->json();
            $raw  = collect(data_get($json, 'data.0.cuaca', []))
                ->flatten(1)
                ->map(fn($item) => [
                    'local_datetime' => $item['local_datetime'],
                    'utc_datetime'   => $item['utc_datetime'],
                    'suhu'           => $item['t'],
                    'kelembapan'     => $item['hu'],
                    'kecepatan_angin' => round($item['ws'] * 0.2778, 1), // knot → m/s
                    'arah_angin_deg' => $item['wd_deg'],
                    'arah_angin'     => $item['wd'],
                    'curah_hujan'    => $item['tp'],
                    'cuaca'          => $item['weather_desc'],
                    'cuaca_icon'     => $item['image'],
                ])
                ->values()
                ->toArray();

            return [
                'label'      => $label,
                'adm4'       => $adm4,
                'prakiraan'  => $raw,
                'updated_at' => now()->toDateTimeString(),
            ];
        } catch (\Throwable $e) {
            return ['label' => $label, 'prakiraan' => [], 'error' => $e->getMessage()];
        }
    }

    public function getAll()
    {
        return Cache::remember('bmkg_all', 3600, function () {
            $result = [];
            foreach (self::WILAYAH as $key => $wilayah) {
                $result[$key] = $this->fetch($wilayah['adm4'], $wilayah['label']);
            }

            return $result;
        });
    }


    public function getCurrent()
    {
        $all = $this->getAll();
        $now = Carbon::now('Asia/Pontianak');
        $closest = null;
        $minDiff = PHP_INT_MAX;


        foreach ($all as $wilayah) {
            foreach ($wilayah['prakiraan'] ?? [] as $item) {
                $dt = Carbon::parse($item['local_datetime'], 'Asia/Pontianak');
                $diff = abs($now->diffInMinutes($dt));
                if ($diff < $minDiff) {
                    $minDiff = $diff;
                    $closest = $item;
                }
            }
        }

        return $closest ?? [];
    }

    public function getByWilayah(string $key)
    {
        $all = $this->getAll();
        return $all[$key] ?? [];
    }

    public function clearCache()
    {
        Cache::forget('bmkg_all');
    }
}

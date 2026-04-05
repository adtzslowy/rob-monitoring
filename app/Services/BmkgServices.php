<?php

namespace App\Services;

use App\Models\BmkgReading;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BmkgServices
{
    const WILAYAH = [
        'delta_pawan' => ['adm4' => '61.04.16.1001', 'label' => 'Delta Pawan'],
        'sungai_awan' => ['adm4' => '61.04.15.1001', 'label' => 'Sungai Awan'],
        'benua_kayong' => ['adm4' => '61.04.02.1001', 'label' => 'Benua Kayong'],
    ];

    public function getAll(): array
    {
        $cached = Cache::get('bmkg_all');
        if ($cached) {
            return $cached;
        }

        foreach (self::WILAYAH as $key => $wilayah) {
            $data = $this->getFromDatabase($key);
            if (! empty($data)) {
                $cached[$key] = $data;

                continue;
            }
            $cached[$key] = $this->fetchFromApi($key, $wilayah);
        }

        Cache::put('bmkg_all', $cached, 3600);

        return $cached;
    }

    private function getFromDatabase(string $key): array
    {
        $readings = BmkgReading::where('wilayah', $key)
            ->orderBy('local_datetime')
            ->get();

        if ($readings->isEmpty()) {
            return [];
        }

        $updatedAt = $readings->max('fetched_at');

        return [
            'label' => self::WILAYAH[$key]['label'] ?? $key,
            'prakiraan' => $readings->map(fn ($r) => [
                'local_datetime' => $r->local_datetime,
                'utc_datetime' => $r->utc_datetime,
                'suhu' => $r->suhu,
                'kelembapan' => $r->kelembapan,
                'kecepatan_angin' => $r->kecepatan_angin,
                'arah_angin_deg' => $r->arah_angin_deg,
                'arah_angin' => $r->arah_angin,
                'curah_hujan' => $r->curah_hujan,
                'cuaca' => $r->cuaca,
                'cuaca_icon' => $r->cuaca_icon,
            ])->values()->toArray(),
            'updated_at' => $updatedAt,
        ];
    }

    private function fetchFromApi(string $key, array $wilayah): array
    {
        try {
            $response = Http::timeout(10)
                ->get('https://api.bmkg.go.id/publik/prakiraan-cuaca', [
                    'adm4' => $wilayah['adm4'],
                ]);

            if (! $response->ok()) {
                return ['label' => $wilayah['label'], 'prakiraan' => [], 'error' => 'HTTP '.$response->status()];
            }

            $json = $response->json();
            $raw = collect(data_get($json, 'data.0.cuaca', []))
                ->flatten(1)
                ->map(fn ($item) => [
                    'local_datetime' => $item['local_datetime'],
                    'utc_datetime' => $item['utc_datetime'],
                    'suhu' => $item['t'],
                    'kelembapan' => $item['hu'],
                    'kecepatan_angin' => round($item['ws'] * 0.2778, 1),
                    'arah_angin_deg' => $item['wd_deg'],
                    'arah_angin' => $item['wd'],
                    'curah_hujan' => $item['tp'],
                    'cuaca' => $item['weather_desc'],
                    'cuaca_icon' => $item['image'],
                ])
                ->values()
                ->toArray();

            return [
                'label' => $wilayah['label'],
                'adm4' => $wilayah['adm4'],
                'prakiraan' => $raw,
                'updated_at' => now()->toDateTimeString(),
            ];
        } catch (\Throwable $e) {
            return ['label' => $wilayah['label'], 'prakiraan' => [], 'error' => $e->getMessage()];
        }
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

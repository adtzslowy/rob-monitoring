<?php

namespace App\Console\Commands;

use App\Models\BmkgReading;
use App\Services\BmkgServices;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncBmkg extends Command
{
    protected $signature = 'bmkg:sync {wilayah? : Wilayah key}';

    protected $description = 'Fetch and store BMKG data to database';

    public function handle()
    {
        $wilayahKey = $this->argument('wilayah');
        $wilayahs = $wilayahKey
            ? [BmkgServices::WILAYAH[$wilayahKey] ?? null]
            : BmkgServices::WILAYAH;

        if (empty($wilayahs)) {
            $this->error('Wilayah not found');

            return 1;
        }

        foreach ($wilayahs as $key => $wilayah) {
            if (! $wilayah) {
                continue;
            }

            $this->info("Fetching {$wilayah['label']}...");
            $this->fetchWilayah($key, $wilayah);
        }

        $this->info('Done!');

        return 0;
    }

    private function fetchWilayah(string $key, array $wilayah): void
    {
        try {
            $response = Http::timeout(30)
                ->get('https://api.bmkg.go.id/publik/prakiraan-cuaca', [
                    'adm4' => $wilayah['adm4'],
                ]);

            if (! $response->ok()) {
                $this->warn("HTTP {$response->status()}");

                return;
            }

            $json = $response->json();
            $raw = collect(data_get($json, 'data.0.cuaca', []))
                ->flatten(1);

            $fetchedAt = now();

            foreach ($raw as $item) {
                if (empty($item['local_datetime'])) {
                    continue;
                }

                BmkgReading::updateOrCreate(
                    [
                        'wilayah' => $key,
                        'local_datetime' => $item['local_datetime'],
                    ],
                    [
                        'adm4' => $wilayah['adm4'],
                        'utc_datetime' => $item['utc_datetime'] ?? null,
                        'suhu' => $item['t'] ?? null,
                        'kelembapan' => $item['hu'] ?? null,
                        'kecepatan_angin' => isset($item['ws'])
                            ? round($item['ws'] * 0.2778, 1)
                            : null,
                        'arah_angin_deg' => $item['wd_deg'] ?? null,
                        'arah_angin' => $item['wd'] ?? null,
                        'curah_hujan' => $item['tp'] ?? null,
                        'cuaca' => $item['weather_desc'] ?? null,
                        'cuaca_icon' => $item['image'] ?? null,
                        'fetched_at' => $fetchedAt,
                    ]
                );
            }

            $this->info('Stored '.$raw->count()." records for {$key}");
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
        }
    }
}

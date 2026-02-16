<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Cache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class fetchData extends Command
{
    protected $signature = 'fetch:alat';

    protected $description = 'Fetch data from iot to api';

    public function handle()
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get(config('services.iot.url'));

            if (!$response->successful()) {
                Log::error('IoT API failed: ' . $response->status());
                return;
            }

            $json = $response->json();
            $data = $json['data'] ?? null;

            if (!$data) {
                Log::warning('Invalid IoT structure');
                return;
            }

            // Simpan ke cache (update setiap detik)
            Cache::put('iot_latest_data', [
                'project'         => $json['project'] ?? 'ROB',
                'timestamp'       => $json['timestamp'] ?? now(),
                'suhu'            => $data['suhu'] ?? null,
                'tekanan_udara'   => $data['tekanan_udara'] ?? null,
                'kelembapan'      => $data['kelembapan'] ?? null,
                'ketinggian_air'  => $data['ketinggian_air'] ?? null,
                'arah_angin'      => $data['arah_angin'] ?? null,
                'kecepatan_angin' => $data['kecepatan_angin'] ?? null,
            ], now()->addSeconds(10));
        } catch (\Throwable $e) {
            Log::error('IoT fetch exception: ' . $e->getMessage());
        }
    }
}

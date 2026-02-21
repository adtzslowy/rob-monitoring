<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\SensorReading;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncIot extends Command
{
    protected $signature = 'app:sync-iot';
    protected $description = 'Fetch IoT data and store into database';

    public function handle()
    {
        $this->info("IoT Sync started");

        $url = config('services.iot.url');

        if (!$url) {
            $this->error("IOT_URL not configured");
            return;
        }

        try {

            // ================= FETCH API =================
            $response = Http::withOptions([
                    'allow_redirects' => true,
                ])
                ->timeout(20)
                ->acceptJson()
                ->get($url);

            $this->info("Status: " . $response->status());

            if (!$response->successful()) {
                $this->error("API request failed");
                return;
            }

            $json = $response->json();

            if (!isset($json['devices'])) {
                $this->error("Invalid API structure");
                return;
            }

            $this->info("Devices count: " . count($json['devices']));

            // ================= LOOP DEVICES =================
            foreach ($json['devices'] as $deviceName => $deviceData) {

                $this->line("Processing {$deviceName}");

                // 1️⃣ Update / Create Device
                $device = Device::updateOrCreate(
                    ['name' => $deviceName],
                    [
                        'status' => $deviceData['status'],
                        'last_seen' => $deviceData['last_seen'],
                    ]
                );

                // Skip offline devices
                if ($deviceData['status'] !== 'online') {
                    $this->warn("Skipped {$deviceName} (offline)");
                    continue;
                }

                $timestamp = $deviceData['last_seen'];

                if (!$timestamp) {
                    $this->warn("Skipped {$deviceName} (no timestamp)");
                    continue;
                }

                // 2️⃣ Insert reading (anti duplicate)
                $exists = SensorReading::where('device_id', $device->id)
                    ->where('timestamp', $timestamp)
                    ->exists();

                if ($exists) {
                    $this->info("Duplicate skipped for {$deviceName}");
                    continue;
                }

                SensorReading::create([
                    'project'         => $json['project'] ?? null,
                    'device_id'       => $device->id,
                    'timestamp'       => $timestamp,
                    'suhu'            => $deviceData['suhu'],
                    'tekanan_udara'   => $deviceData['tekanan_udara'],
                    'kelembapan'      => $deviceData['kelembapan'],
                    'ketinggian_air'  => $deviceData['ketinggian_air'],
                    'arah_angin'      => $deviceData['arah_angin'],
                    'kecepatan_angin' => $deviceData['kecepatan_angin'],
                ]);

                $this->info("Saved {$deviceName}");
            }

            $this->info("IoT Sync completed");

        } catch (\Throwable $e) {
            Log::error("IoT Sync error: " . $e->getMessage());
            $this->error("ERROR: " . $e->getMessage());
        }
    }
}

<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Notification;
use App\Models\SensorReading;
use App\Services\FuzzyRiskServices;
use App\Services\TelegramServices;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CheckSensorStatus implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $devices = Device::all();

        if ($devices->isEmpty()) return;


        $fuzzy = app(FuzzyRiskServices::class);
        $telegram = app(TelegramServices::class);

        foreach ($devices as $device) {
            $latest = SensorReading::query()->where('device_id', $device->id)->latest('timestamp')->first();

            if (!$latest) continue;

            $result = $fuzzy->evaluate([
                'ketinggian_air'  => (float) ($latest->ketinggian_air  ?? 0),
                'tekanan_udara'   => (float) ($latest->tekanan_udara   ?? 0),
                'kecepatan_angin' => (float) ($latest->kecepatan_angin ?? 0),
                'arah_angin'      => (float) ($latest->arah_angin      ?? 0),
            ]);

            $currentRisk = $result['label'];
            $cacheKeyPrev = "sensor_risk_prev_{$device->id}";
            $previousRisk = Cache::get($cacheKeyPrev, 'AMAN');

            Cache::put($cacheKeyPrev, $currentRisk, now()->addHours(2));

            if ($previousRisk === $currentRisk) continue;

            $alertStatus = ['WASPADA', 'SIAGA', 'BAHAYA'];
            if (!in_array($currentRisk, $alertStatus, true)) continue;

            $cacheKey = "telegram_notif_{$device->id}_{$currentRisk}";
            if (Cache::has($cacheKey)) continue;

            $settings = Notification::query()->where('notifikasi_aktif', true)
                ->whereNotNull('telegram_chat_id')
                ->where('telegram_chat_id', '!=', '')
                ->when($currentRisk === 'WASPADA', fn($q) => $q->where('notifikasi_waspada', true))
                ->when($currentRisk === 'SIAGA',   fn($q) => $q->where('notifikasi_siaga',   true))
                ->when($currentRisk === 'BAHAYA',  fn($q) => $q->where('notifikasi_bahaya',  true))
                ->get();

            if ($settings->isEmpty()) continue;

            $deviceName = $device->alias ?? $device->name ?? "Device #{$device->id}";

            foreach ($settings as $setting) {
                $telegram->statusAlert(
                    chatId:     $setting->telegram_chat_id,
                    status:     $currentRisk,
                    deviceName: $deviceName,
                    sensorData: [
                        'ketinggian_air'  => $latest->ketinggian_air,
                        'suhu'            => $latest->suhu,
                        'kelembapan'      => $latest->kelembapan,
                        'tekanan_udara'   => $latest->tekanan_udara,
                        'kecepatan_angin' => $latest->kecepatan_angin,
                        'arah_angin'      => $latest->arah_angin,
                    ],
                );
            }

            Cache::put($cacheKey, true, now()->addMinutes(5));

            Log::info("CheckSensorStatus: notifikasi dikirim", [
                'device_id'    => $device->id,
                'device_name'  => $deviceName,
                'status'       => $currentRisk,
                'prev_status'  => $previousRisk,
                'recipients'   => $settings->count(),
            ]);
        }
    }
}

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

    public function handle(): void
    {
        $devices = Device::all();

        if ($devices->isEmpty()) return;

        $fuzzy    = app(FuzzyRiskServices::class);
        $telegram = app(TelegramServices::class);

        $digestPerUser = [];

        foreach ($devices as $device) {
            $latest = SensorReading::query()
                ->where('device_id', $device->id)
                ->latest('timestamp')
                ->first();

            if (!$latest) continue;


            $result = $fuzzy->evaluate([
                'ketinggian_air'  => (float) ($latest->ketinggian_air  ?? 0),
                'tekanan_udara'   => (float) ($latest->tekanan_udara   ?? 0),
                'kecepatan_angin' => (float) ($latest->kecepatan_angin ?? 0),
                'arah_angin'      => (float) ($latest->arah_angin      ?? 0),
            ]);

            $currentRisk  = $result['label'];
            $cacheKeyPrev = "sensor_risk_prev_{$device->id}";
            $previousRisk = Cache::get($cacheKeyPrev, 'AMAN');

            Cache::put($cacheKeyPrev, $currentRisk, now()->addHours(2));

            if ($previousRisk === $currentRisk) continue;

            $alertStatuses = ['WASPADA', 'SIAGA', 'BAHAYA'];
            if (!in_array($currentRisk, $alertStatuses, true)) continue;

            $cacheKey = "telegram_notif_{$device->id}_{$currentRisk}";
            if (Cache::has($cacheKey)) continue;

            Cache::put($cacheKey, true, now()->addMinutes(30));

            $deviceName = $device->alias ?? $device->name ?? "Device #{$device->id}";

            // Ambil semua user yang aktif notifikasinya
            $settings = Notification::query()
                ->where('notifikasi_aktif', true)
                ->whereNotNull('telegram_chat_id')
                ->where('telegram_chat_id', '!=', '')
                ->when($currentRisk === 'WASPADA', fn($q) => $q->where('notifikasi_waspada', true))
                ->when($currentRisk === 'SIAGA',   fn($q) => $q->where('notifikasi_siaga',   true))
                ->when($currentRisk === 'BAHAYA',  fn($q) => $q->where('notifikasi_bahaya',  true))
                ->get();

            if ($settings->isEmpty()) continue;

            // Kumpulkan ke digest per user
            foreach ($settings as $setting) {
                $chatId = $setting->telegram_chat_id;

                if (!isset($digestPerUser[$chatId])) {
                    $digestPerUser[$chatId] = [];
                }

                $digestPerUser[$chatId][] = [
                    'device'      => $deviceName,
                    'status'      => $currentRisk,
                    'prev_status' => $previousRisk,
                    'data'        => [
                        'ketinggian_air'  => $latest->ketinggian_air,
                        'suhu'            => $latest->suhu,
                        'kelembapan'      => $latest->kelembapan,
                        'tekanan_udara'   => $latest->tekanan_udara,
                        'kecepatan_angin' => $latest->kecepatan_angin,
                        'arah_angin'      => $latest->arah_angin,
                    ],
                ];
            }

            Log::info("CheckSensorStatus: alert dikumpulkan", [
                'device_id'   => $device->id,
                'device_name' => $deviceName,
                'status'      => $currentRisk,
                'prev_status' => $previousRisk,
            ]);
        }

        // Kirim digest — 1 pesan per user untuk semua device
        foreach ($digestPerUser as $chatId => $alerts) {
            $message = $this->buildDigestMessage($alerts);
            $telegram->send($chatId, $message);

            Log::info("CheckSensorStatus: digest dikirim", [
                'chat_id'      => $chatId,
                'alert_count'  => count($alerts),
            ]);
        }
    }

    /**
     * Buat pesan digest untuk semua alert.
     */
    private function buildDigestMessage(array $alerts): string
    {
        $waktu = now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB';
        $count = count($alerts);

        $message  = "🚨 <b>ROB MONITORING — ALERT DIGEST</b>\n";
        $message .= "━━━━━━━━━━━━━━━━\n";
        $message .= "🕐 <b>Waktu:</b> {$waktu}\n";
        $message .= "📊 <b>Total Alert:</b> {$count} device\n";
        $message .= "━━━━━━━━━━━━━━━━\n\n";

        foreach ($alerts as $i => $alert) {
            $no     = $i + 1;
            $emoji  = match ($alert['status']) {
                'BAHAYA'  => '🔴',
                'SIAGA'   => '🟠',
                'WASPADA' => '🟡',
                default   => '🟢',
            };

            $message .= "{$emoji} <b>#{$no} {$alert['device']}</b>\n";
            $message .= "   Status: <b>{$alert['status']}</b> (sebelumnya: {$alert['prev_status']})\n";

            $data = $alert['data'];

            if ($data['ketinggian_air'] !== null)  $message .= "   💧 Ketinggian Air: <b>{$data['ketinggian_air']} cm</b>\n";
            if ($data['suhu'] !== null)             $message .= "   🌡️ Suhu: <b>{$data['suhu']} °C</b>\n";
            if ($data['kelembapan'] !== null)             $message .= "   💧 Kelembapan: <b>{$data['kelembapan']} °C</b>\n";
            if ($data['kecepatan_angin'] !== null)  $message .= "   💨 Kec. Angin: <b>{$data['kecepatan_angin']} m/s</b>\n";
            if ($data['tekanan_udara'] !== null)    $message .= "   🌀 Tekanan: <b>{$data['tekanan_udara']} hPa</b>\n";

            if ($i < count($alerts) - 1) {
                $message .= "\n";
            }
        }

        $message .= "\n━━━━━━━━━━━━━━━━\n";
        $message .= "<i>ROB Monitoring — Early Warning System</i>";

        return $message;
    }
}
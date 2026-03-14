<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class TelegramServices
{
    protected string $token;
    protected string $baseUrl;

    public function __construct()
    {
        $this->token = config('services.telegram.token');
        $this->baseUrl = 'https://api.telegram.org/bot{$this->token}';
    }

     /**
     * Kirim pesan teks ke chat_id tertentu.
     */

     public function send(string $chatId, string $message)
     {
        try {
            $response = Http::post("{$this->baseUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if (!$response->successful() || !$response->json('ok')) {
                Log::warning('Telegram gagal kirim pesan', [
                    'chat_id' => $chatId,
                    'response' => $response->json(),
                ]);
                return false;
            }
            return true;
            
        } catch (\Exception $e) {
            Log::error('Telegram exeption: ' . $e->getMessage());
            return false;
        }
     }

     public function statusAlert(
        string $chatId,
        string $status,
        string $deviceName,
        array $sensorData = [],
     ) {
        $emoji = match($status) {
            'BAHAYA' => '🔴',
            'WASPADA' => '🟠',
            'SIAGA' => '🟡',
            default => '🟢',
        };

        $waktu = now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB';

        $message  = "{$emoji} <b>ROB MONITORING ALERT</b>\n";
        $message .= "━━━━━━━━━━━━━━━━\n";
        $message .= "📍 <b>Device:</b> {$deviceName}\n";
        $message .= "⚠️ <b>Status:</b> <b>{$status}</b>\n";
        $message .= "🕐 <b>Waktu:</b> {$waktu}\n";
        $message .= "━━━━━━━━━━━━━━━━\n";
        $message .= "📊 <b>Data Sensor:</b>\n";

        $sensorLabels = [
            'ketinggian_air'  => ['label' => 'Ketinggian Air', 'unit' => 'cm'],
            'suhu'            => ['label' => 'Suhu',           'unit' => '°C'],
            'kelembapan'      => ['label' => 'Kelembapan',     'unit' => '%'],
            'tekanan_udara'   => ['label' => 'Tekanan Udara',  'unit' => 'hPa'],
            'kecepatan_angin' => ['label' => 'Kec. Angin',     'unit' => 'm/s'],
            'arah_angin'      => ['label' => 'Arah Angin',     'unit' => '°'],
        ];

        foreach ($sensorLabels as $key => $info) {
            if (isset($sensorData[$key])) {
                $message .= "  • {$info['label']}: <b>{$sensorData[$key]} {$info['unit']}</b>\n";
            }
        }

        $message .= "━━━━━━━━━━━━━━━━\n";
        $message .= "<i>ROB Monitoring — Early Warning System</i>";

        return $this->send($chatId, $message);
     }

     public function sendTest(string $chatId)
     {
        $waktu = now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB';

        $message = "✅ <b>Test Notifikasi Berhasil!</b>\n\n";
        $message .= "Notifikasi ROB Monitoring sudah terhubung ke akun Telegram Anda.\n\n";
        $message .= "🕐 <b>Waktu:</b> {$waktu}\n";
        $message .= "<i>ROB Monitoring — Early Warning System</i>";

        return $this->send($chatId, $message);
     }
}
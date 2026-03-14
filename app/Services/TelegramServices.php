<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramServices
{
    protected string $token;
    protected string $baseUrl;

    public function __construct()
    {
        $this->token   = config('services.telegram.token');
        $this->baseUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Kirim pesan teks ke chat_id tertentu.
     */
    public function send(string $chatId, string $message): bool
    {
        try {
            $response = Http::post("{$this->baseUrl}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => 'HTML',
            ]);

            if (!$response->successful() || !$response->json('ok')) {
                Log::warning('Telegram gagal kirim pesan', [
                    'chat_id'  => $chatId,
                    'response' => $response->json(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Telegram exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi status sensor ROB.
     */
    public function statusAlert(
        string $chatId,
        string $status,
        string $deviceName,
        array  $sensorData = []
    ): bool {
        $emoji = match ($status) {
            'BAHAYA'  => '🔴',
            'SIAGA'   => '🟠',
            'WASPADA' => '🟡',
            default   => '🟢',
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

    /**
     * Kirim pesan test untuk uji koneksi.
     */
    public function sendTest(string $chatId): bool
    {
        $waktu   = now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB';
        $message  = "✅ <b>Test Notifikasi Berhasil!</b>\n\n";
        $message .= "Notifikasi ROB Monitoring sudah terhubung ke akun Telegram Anda.\n\n";
        $message .= "🕐 <b>Waktu:</b> {$waktu}\n";
        $message .= "<i>ROB Monitoring — Early Warning System</i>";

        return $this->send($chatId, $message);
    }

    /**
     * Handle command dari Telegram webhook.
     * Mendukung: /start, /id
     */
    public function handleCommand(array $update): void
    {
        $message = $update['message'] ?? null;

        if (!$message) return;

        $chatId = (string) ($message['chat']['id'] ?? '');
        $text   = trim($message['text'] ?? '');
        $name   = $message['from']['first_name'] ?? 'Pengguna';

        if (empty($chatId)) return;

        if (str_starts_with($text, '/start')) {
            $this->send($chatId,
                "👋 Halo <b>{$name}</b>!\n\n" .
                "Selamat datang di <b>ROB Monitoring Bot</b> 🌊\n\n" .
                "Bot ini akan mengirimkan peringatan dini banjir ROB ke Telegram kamu.\n\n" .
                "📌 Ketik /id untuk mendapatkan Chat ID kamu.\n" .
                "📌 Salin Chat ID tersebut ke halaman <b>Pengaturan</b> di aplikasi ROB Monitoring."
            );
            return;
        }

        if (str_starts_with($text, '/id')) {
            $this->send($chatId,
                "🆔 <b>Chat ID Telegram kamu:</b>\n\n" .
                "<code>{$chatId}</code>\n\n" .
                "📋 <i>Tap angka di atas untuk menyalin.</i>\n\n" .
                "Paste Chat ID tersebut di:\n" .
                "<b>Pengaturan → Notifikasi Telegram → Telegram Chat ID</b>\n\n" .
                "<i>ROB Monitoring — Early Warning System</i>"
            );
            return;
        }

        // Command tidak dikenal
        $this->send($chatId,
            "❓ Command tidak dikenal.\n\n" .
            "Gunakan:\n" .
            "• /start — Mulai bot\n" .
            "• /id — Dapatkan Chat ID kamu"
        );
    }

    /**
     * Daftarkan webhook URL ke Telegram.
     */
    public function setWebhook(string $url): bool
    {
        try {
            $response = Http::post("{$this->baseUrl}/setWebhook", [
                'url' => $url,
            ]);

            return $response->successful() && $response->json('ok');
        } catch (\Exception $e) {
            Log::error('Telegram setWebhook error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Hapus webhook (untuk mode polling / development).
     */
    public function deleteWebhook(): bool
    {
        try {
            $response = Http::post("{$this->baseUrl}/deleteWebhook");
            return $response->successful() && $response->json('ok');
        } catch (\Exception $e) {
            Log::error('Telegram deleteWebhook error: ' . $e->getMessage());
            return false;
        }
    }
}
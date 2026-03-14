<?php

namespace App\Console\Commands;

use App\Services\TelegramServices;
use Illuminate\Console\Command;

class TelegramWebHook extends Command
{
    protected $signature = 'telegram:webhok {action : set atau delete}';
    protected $description = 'Atur webhook Telegram Bot (set/delete)';

    /**
     * Execute the console command.
     */
    public function handle(TelegramServices $telegram)
    {
        $action = $this->argument('action');

        if ($action === 'set') {
            $url = config('app.url') . '/telegram/webhook';
            $this->info("Mendaftarkan webhook ke: {$url}");

            $success = $telegram->setWebhook($url);

            if ($success) {
                $this->info('✅ Webhook berhasil didaftarkan!');
            } else {
                $this->error('❌ Gagal mendaftarkan webhook. Cek token dan URL');
            }
        } elseif($action === 'delete') {
            $success = $telegram->deleteWebhook();

             if ($success) {
                $this->info('✅ Webhook berhasil dihapus!');
            } else {
                $this->error('❌ Gagal menghapus webhook.');
            }
        } else {
            $this->error('Action tidak valid. Gunakan: set atau delete');
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\DashboardLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SaveData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iot:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save data from API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = Cache::get('iot_latest_data');

        if (!$data) {
            $this->warn('No data in cache to save');
            return;
        }

        try {
            DashboardLog::create($data);
            $this->info('Data saved to dashboard_logs at ' . now());
        } catch (\Throwable $e) {
            Log::error('Failed to save to dashboard_logs: ' . $e->getMessage());
        }
    }
}

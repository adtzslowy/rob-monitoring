<?php

namespace App\Livewire\Admin;

use App\Models\Notification;
use App\Services\TelegramServices;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Pengaturan")]
class Pengaturan extends Component
{
    // Notifikasi
    public string $telegram_chat_id  = '';
    public bool   $notifikasi_aktif  = false;
    public bool   $notifikasi_waspada = true;
    public bool   $notifikasi_siaga   = true;
    public bool   $notifikasi_bahaya  = true;

    public bool $isAdmin;

    // Threshold (admin only)
    public array $threshold = [
        'ketinggian_air'  => ['waspada' => '', 'bahaya' => ''],
        'suhu'            => ['waspada' => '', 'bahaya' => ''],
        'kelembapan'      => ['waspada' => '', 'bahaya' => ''],
        'tekanan_udara'   => ['waspada' => '', 'bahaya' => ''],
        'kecepatan_angin' => ['waspada' => '', 'bahaya' => ''],
        'arah_angin'      => ['waspada' => '', 'bahaya' => ''],
    ];

    public function mount(): void
    {
        $user          = auth()->user();
        $this->isAdmin = $user?->can('manage devices') ?? false;

        // Load notifikasi setting
        $setting = Notification::query()->firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'telegram_chat_id'   => '',
                'notifikasi_aktif'   => false,
                'notifikasi_waspada' => true,
                'notifikasi_siaga'   => true,
                'notifikasi_bahaya'  => true,
            ]
        );

        $this->telegram_chat_id   = $setting->telegram_chat_id ?? '';
        $this->notifikasi_aktif   = (bool) $setting->notifikasi_aktif;
        $this->notifikasi_waspada = (bool) $setting->notifikasi_waspada;
        $this->notifikasi_siaga   = (bool) $setting->notifikasi_siaga;
        $this->notifikasi_bahaya  = (bool) $setting->notifikasi_bahaya;

        // Load threshold (admin only) dari config/database
        if ($this->isAdmin) {
            $this->threshold = $this->loadThreshold();
        }
    }

    private function loadThreshold(): array
    {
        // Ambil dari cache/config — sesuaikan dengan implementasi kamu
        return cache()->get('fuzzy_threshold', [
            'ketinggian_air'  => ['waspada' => 80,   'bahaya' => 100],
            'suhu'            => ['waspada' => 35,   'bahaya' => 40],
            'kelembapan'      => ['waspada' => 80,   'bahaya' => 95],
            'tekanan_udara'   => ['waspada' => 990,  'bahaya' => 980],
            'kecepatan_angin' => ['waspada' => 10,   'bahaya' => 20],
            'arah_angin'      => ['waspada' => 180,  'bahaya' => 270],
        ]);
    }

    public function saveThreshold(): void
    {
        if (!$this->isAdmin) {
            abort(403);
        }

        $this->validate([
            'threshold.ketinggian_air.waspada'  => 'required|numeric',
            'threshold.ketinggian_air.bahaya'   => 'required|numeric',
            'threshold.suhu.waspada'            => 'required|numeric',
            'threshold.suhu.bahaya'             => 'required|numeric',
            'threshold.kelembapan.waspada'      => 'required|numeric',
            'threshold.kelembapan.bahaya'       => 'required|numeric',
            'threshold.tekanan_udara.waspada'   => 'required|numeric',
            'threshold.tekanan_udara.bahaya'    => 'required|numeric',
            'threshold.kecepatan_angin.waspada' => 'required|numeric',
            'threshold.kecepatan_angin.bahaya'  => 'required|numeric',
            'threshold.arah_angin.waspada'      => 'required|numeric|min:0|max:360',
            'threshold.arah_angin.bahaya'       => 'required|numeric|min:0|max:360',
        ]);

        // Simpan ke cache — sesuaikan jika pakai DB
        cache()->put('fuzzy_threshold', $this->threshold);

        session()->flash('success', 'Threshold sensor berhasil disimpan!');
    }

    public function toggleNotifikasi(): void
    {
        $this->notifikasi_aktif = !$this->notifikasi_aktif;
    }

    public function saveNotifikasi(): void
    {
        $this->validate([
            'telegram_chat_id'   => 'required|string|max:50',
            'notifikasi_waspada' => 'boolean',
            'notifikasi_siaga'   => 'boolean',
            'notifikasi_bahaya'  => 'boolean',
        ]);

        Notification::query()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'telegram_chat_id'   => $this->telegram_chat_id,
                'notifikasi_aktif'   => $this->notifikasi_aktif,
                'notifikasi_waspada' => $this->notifikasi_waspada,
                'notifikasi_siaga'   => $this->notifikasi_siaga,
                'notifikasi_bahaya'  => $this->notifikasi_bahaya,
            ]
        );

        session()->flash('success', 'Pengaturan notifikasi berhasil disimpan!');
    }

    public function testNotifikasi(): void
    {
        $this->validate([
            'telegram_chat_id' => 'required|string|max:50',
        ]);

        $telegram = app(TelegramServices::class);
        $success  = $telegram->sendTest($this->telegram_chat_id);

        if ($success) {
            session()->flash('success', 'Pesan test berhasil dikirim ke Telegram!');
        } else {
            session()->flash('error', 'Gagal kirim pesan. Cek Chat ID dan koneksi internet.');
        }
    }

    public function render()
    {
        return view('livewire.admin.pengaturan');
    }
}
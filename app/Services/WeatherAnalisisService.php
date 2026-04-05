<?php

namespace App\Services;

use App\Models\SensorReading;
use Carbon\Carbon;

class WeatherAnalisisService
{
    private const THRESHOLD = [
        'ketinggian_air' => ['waspada' => 130, 'bahaya' => 170],
        'suhu' => ['waspada' => 32,  'bahaya' => 35],
        'kelembapan' => ['waspada' => 85,  'bahaya' => 95],
        'tekanan_udara' => ['waspada' => 1005, 'bahaya' => 995],
        'kecepatan_angin' => ['waspada' => 8,   'bahaya' => 15],
    ];

    public function analyze(int $deviceId, string $wilayah = 'delta_pawan'): array
    {
        $sensorData = $this->getLatestSensorData($deviceId);

        if (empty($sensorData)) {
            return [
                'analisa' => null,
                'timestamp' => now()->toDateTimeString(),
            ];
        }

        $fuzzyResult = app(FuzzyRiskServices::class)->evaluate($sensorData);

        $bmkgCurrent = $this->getCurrentBmkg($wilayah);

        $analisa = $this->generateAnalisa($sensorData, $bmkgCurrent, $fuzzyResult);

        return [
            'sensor' => $sensorData,
            'bmkg' => $bmkgCurrent,
            'fuzzy' => $fuzzyResult,
            'analisa' => $analisa,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    public function getLatestSensorData(int $deviceId): array
    {
        $reading = SensorReading::where('device_id', $deviceId)
            ->orderBy('timestamp', 'desc')
            ->first();

        if (! $reading) {
            return [];
        }

        return [
            'suhu' => (float) $reading->suhu,
            'kelembapan' => (float) $reading->kelembapan,
            'tekanan_udara' => (float) $reading->tekanan_udara,
            'ketinggian_air' => (float) $reading->ketinggian_air,
            'kecepatan_angin' => (float) $reading->kecepatan_angin,
            'arah_angin' => (float) $reading->arah_angin,
        ];
    }

    private function getCurrentBmkg(string $wilayah): array
    {
        $bmkgService = app(BmkgServices::class);
        $data = $bmkgService->getByWilayah($wilayah);

        $prakiraan = $data['prakiraan'] ?? [];

        if (empty($prakiraan)) {
            return [];
        }

        $now = Carbon::now('Asia/Pontianak');
        $closest = null;
        $minDiff = PHP_INT_MAX;

        foreach ($prakiraan as $item) {
            $dt = Carbon::parse($item['local_datetime'], 'Asia/Pontianak');
            $diff = abs($now->diffInMinutes($dt));
            if ($diff < $minDiff) {
                $minDiff = $diff;
                $closest = $item;
            }
        }

        return $closest ?? [];
    }

    private function generateAnalisa(array $sensor, array $bmkg, array $fuzzy): array
    {
        $status = $fuzzy['label'] ?? 'AMAN';
        $score = $fuzzy['score'] ?? 0;

        $kondisi = $this->buildKondisiText($sensor, $bmkg);
        $resiko = $this->buildRisikoText($sensor, $bmkg, $fuzzy);
        $rekomendasi = $this->buildRekomendasiText($status, $fuzzy);
        $ringkasan = $this->buildRingkasanText($status, $score);

        return [
            'status' => $status,
            'score' => $score,
            'ringkasan' => $ringkasan,
            'kondisi' => $kondisi,
            'resiko' => $resiko,
            'rekomendasi' => $rekomendasi,
        ];
    }

    private function buildKondisiText(array $sensor, array $bmkg): string
    {
        $parts = [];

        if (isset($sensor['suhu'])) {
            $parts[] = "Suhu {$sensor['suhu']}°C";
        }
        if (isset($sensor['kelembapan'])) {
            $parts[] = "Kelembapan {$sensor['kelembapan']}%";
        }
        if (isset($sensor['ketinggian_air'])) {
            $parts[] = "Ketinggian air {$sensor['ketinggian_air']} cm";
        }
        if (isset($sensor['kecepatan_angin'])) {
            $parts[] = "Kecepatan angin {$sensor['kecepatan_angin']} m/s";
        }
        if (! empty($bmkg['cuaca'])) {
            $parts[] = "Cuaca: {$bmkg['cuaca']}";
        }

        return implode(' • ', $parts);
    }

    private function buildRisikoText(array $sensor, array $bmkg, array $fuzzy): string
    {
        $risiko = [];
        $m = $fuzzy['memberships'] ?? [];

        if (($m['bahaya'] ?? 0) > 0.3) {
            $risiko[] = 'Potensi bahaya tinggi detected';
        }
        if (($m['siaga'] ?? 0) > 0.3) {
            $risiko[] = 'Waspada kondisi siaga';
        }
        if (($m['waspada'] ?? 0) > 0.4) {
            $risiko[] = 'Perlu kewaspadaan ekstra';
        }

        if (isset($sensor['ketinggian_air']) && $sensor['ketinggian_air'] >= self::THRESHOLD['ketinggian_air']['bahaya']) {
            $risiko[] = 'Ketinggian air kritis';
        }
        if (isset($sensor['kecepatan_angin']) && $sensor['kecepatan_angin'] >= self::THRESHOLD['kecepatan_angin']['bahaya']) {
            $risiko[] = 'Angin kencang';
        }

        return empty($risiko) ? 'Tidak ada risiko khusus' : implode("\n", $risiko);
    }

    private function buildRekomendasiText(string $status, array $fuzzy): string
    {
        return match ($status) {
            'BAHAYA' => "Hindari aktivitas di laut\nSiapkan evakuasi\nIkuti informasi resmi",
            'SIAGA' => "Batasi aktivitas laut\nPantau terus informasi cuaca\nSiapkan物资 darurat",
            'WASPADA' => "Waspada aktivitas laut\nPakai alat pengaman\nIkuti perkembangan cuaca",
            default => "Kondisi normal\nTetap pawai informasi cuaca\nNikmati aktivitas laut",
        };
    }

    private function buildRingkasanText(string $status, float $score): string
    {
        return match ($status) {
            'BAHAYA' => "Kondisi {$score}% menunjukkan tingkat BAHAYA. Segera lakukan tindakan pencegahan.",
            'SIAGA' => "Kondisi {$score}% menunjukkan tingkat SIAGA. Waspada dan lakukan persiapan.",
            'WASPADA' => "Kondisi {$score}% menunjukkan tingkat WASPADA. Tetap pantau kondisi cuaca.",
            default => "Kondisi {$score}% dalam kategori AMAN. Kondisi cuaca kondusif untuk aktivitas.",
        };
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class FuzzyRiskServices
{
    /**
     * Load threshold per device dari fetch.
     * Fallback ke nilai default BMKG Ketapang, Kalimantan Barat.
     *
     * Struktur cache key: fuzzy_threshold_{deviceId}
     * Struktur nilai:
     *   [sensor => ['aman' => x, 'waspada' => x, 'siaga' => x, 'bahaya' => x]]
     */
    private function loadThreshold(int $deviceId): array
    {
        $defaults = [
            'ketinggian_air'  => ['aman' =>  80, 'waspada' => 130, 'siaga' => 150, 'bahaya' => 170],
            'suhu'            => ['aman' =>  30, 'waspada' =>  32, 'siaga' =>  34, 'bahaya' =>  36],
            'kelembapan'      => ['aman' =>  80, 'waspada' =>  85, 'siaga' =>  90, 'bahaya' =>  95],
            'tekanan_udara'   => ['aman' => 1013,'waspada' =>1008, 'siaga' =>1003, 'bahaya' => 998],
            'kecepatan_angin' => ['aman' =>   5, 'waspada' =>   8, 'siaga' =>  12, 'bahaya' =>  15],
            'arah_angin'      => ['aman' =>  90, 'waspada' => 180, 'siaga' => 225, 'bahaya' => 270],
        ];

        $saved = Cache::get("fuzzy_threshold_{$deviceId}", []);

        // Merge per-key agar partial override tetap valid
        foreach ($defaults as $sensor => $val) {
            if (!empty($saved[$sensor])) {
                $defaults[$sensor] = array_merge($val, $saved[$sensor]);
            }
        }

        return $defaults;
    }

    /**
     * Evaluasi risk fuzzy untuk satu device.
     *
     * @param  array $input   Data sensor mentah (ketinggian_air, suhu, dll.)
     * @param  int   $deviceId
     * @return array{score: float, label: string, memberships: array, debug: array}
     */
    public function evaluate(array $input, int $deviceId = 0): array
    {
        $t = $this->loadThreshold($deviceId);

        $water     = (float) ($input['ketinggian_air']  ?? 0);
        $temp      = (float) ($input['suhu']            ?? 0);
        $humidity  = (float) ($input['kelembapan']      ?? 0);
        $pressure  = (float) ($input['tekanan_udara']   ?? 0);
        $windSpeed = (float) ($input['kecepatan_angin'] ?? 0);
        $windDir   = (float) ($input['arah_angin']      ?? 0);

        // ── Ketinggian Air (semakin tinggi = semakin bahaya) ──────────────────
        $wA = (float) $t['ketinggian_air']['aman'];
        $wW = (float) $t['ketinggian_air']['waspada'];
        $wS = (float) $t['ketinggian_air']['siaga'];
        $wB = (float) $t['ketinggian_air']['bahaya'];

        $waterLow    = $this->trapDesc($water, 0,   $wA * 0.5, $wW);
        $waterMedium = $this->tri($water,      $wA, $wW,       $wS);
        $waterHigh   = $this->tri($water,      $wW, $wS,       $wB);
        $waterVHigh  = $this->trapAsc($water,  $wS, $wB,       $wB * 1.5);

        // ── Suhu (semakin tinggi = semakin bahaya) ────────────────────────────
        $tA = (float) $t['suhu']['aman'];
        $tW = (float) $t['suhu']['waspada'];
        $tS = (float) $t['suhu']['siaga'];
        $tB = (float) $t['suhu']['bahaya'];

        $tempLow    = $this->trapDesc($temp, 0,  $tA * 0.9, $tW);
        $tempMedium = $this->tri($temp,      $tA, $tW,       $tS);
        $tempHigh   = $this->tri($temp,      $tW, $tS,       $tB);
        $tempVHigh  = $this->trapAsc($temp,  $tS, $tB,       $tB + 5);

        // ── Kelembapan (semakin tinggi = semakin bahaya) ──────────────────────
        $hA = (float) $t['kelembapan']['aman'];
        $hW = (float) $t['kelembapan']['waspada'];
        $hS = (float) $t['kelembapan']['siaga'];
        $hB = (float) $t['kelembapan']['bahaya'];

        $humLow    = $this->trapDesc($humidity, 0,  $hA * 0.9, $hW);
        $humMedium = $this->tri($humidity,      $hA, $hW,       $hS);
        $humHigh   = $this->tri($humidity,      $hW, $hS,       $hB);
        $humVHigh  = $this->trapAsc($humidity,  $hS, $hB,       100);

        // ── Tekanan Udara (semakin rendah = semakin bahaya) ───────────────────
        $pA = (float) max(array_values($t['tekanan_udara'])); // aman = nilai tertinggi
        $pW = (float) $t['tekanan_udara']['waspada'];
        $pS = (float) $t['tekanan_udara']['siaga'];
        $pB = (float) min(array_values($t['tekanan_udara'])); // bahaya = nilai terendah

        $pressureHigh   = $this->trapAsc($pressure,  $pW,       $pA,      $pA + 10); // aman
        $pressureMedium = $this->tri($pressure,      $pS,       $pW,      $pA);
        $pressureLow    = $this->tri($pressure,      $pB,       $pS,      $pW);
        $pressureVLow   = $this->trapDesc($pressure, $pB - 10,  $pB,      $pS);      // bahaya

        // ── Kecepatan Angin (semakin tinggi = semakin bahaya) ─────────────────
        $vA = (float) $t['kecepatan_angin']['aman'];
        $vW = (float) $t['kecepatan_angin']['waspada'];
        $vS = (float) $t['kecepatan_angin']['siaga'];
        $vB = (float) $t['kecepatan_angin']['bahaya'];

        $windLow    = $this->trapDesc($windSpeed, 0,  $vA * 0.5, $vW);
        $windMedium = $this->tri($windSpeed,      $vA, $vW,       $vS);
        $windHigh   = $this->tri($windSpeed,      $vW, $vS,       $vB);
        $windVHigh  = $this->trapAsc($windSpeed,  $vS, $vB,       $vB * 1.5);

        // ── Arah Angin (membership berbasis sektor arah) ──────────────────────
        $onshore    = $this->onshoreMembership($windDir);
        $crossshore = $this->crossshoreMembership($windDir);
        $offshore   = $this->offshoreMembership($windDir);

        // ════════════════════════════════════════════════════════════════════════
        // Aturan Fuzzy (Mamdani / rule-based)
        // ════════════════════════════════════════════════════════════════════════
        $aman    = [];
        $waspada = [];
        $siaga   = [];
        $bahaya  = [];

        // ── AMAN ──
        $aman[] = min($waterLow,   $windLow,    $pressureHigh);
        $aman[] = min($waterLow,   $offshore,   $tempLow);
        $aman[] = min($waterLow,   $humLow,     $pressureHigh);

        // ── WASPADA ──
        $waspada[] = min($waterMedium, $windLow,    $pressureMedium);
        $waspada[] = min($waterLow,    $windMedium, $onshore);
        $waspada[] = min($waterMedium, $offshore);
        $waspada[] = min($waterLow,    $tempMedium, $humMedium);
        $waspada[] = min($waterMedium, $tempLow,    $pressureMedium);

        // ── SIAGA ──
        $siaga[] = min($waterMedium, $windMedium,  $onshore);
        $siaga[] = min($waterHigh,   $windLow,     $pressureMedium);
        $siaga[] = min($waterMedium, $pressureLow, $onshore);
        $siaga[] = min($waterMedium, $windHigh,    $crossshore);
        $siaga[] = min($waterHigh,   $tempMedium,  $humMedium);
        $siaga[] = min($waterMedium, $tempHigh,    $windMedium);

        // ── BAHAYA ──
        $bahaya[] = min($waterHigh,  $windMedium,  $onshore);
        $bahaya[] = min($waterHigh,  $windHigh);
        $bahaya[] = min($waterHigh,  $pressureLow);
        $bahaya[] = min($waterHigh,  $windHigh,    $onshore);
        $bahaya[] = min($waterVHigh, $pressureVLow);
        $bahaya[] = min($waterVHigh, $windVHigh,   $onshore);
        $bahaya[] = min($waterHigh,  $tempVHigh,   $pressureLow);
        $bahaya[] = min($waterMedium,$windHigh,    $pressureLow, $onshore);

        $muAman    = round(max($aman    ?: [0]), 4);
        $muWaspada = round(max($waspada ?: [0]), 4);
        $muSiaga   = round(max($siaga   ?: [0]), 4);
        $muBahaya  = round(max($bahaya  ?: [0]), 4);

        // ── Defuzzifikasi — Weighted Average (Sugeno) ─────────────────────────
        $zAman    = 15;
        $zWaspada = 40;
        $zSiaga   = 68;
        $zBahaya  = 92;

        $num   = ($muAman * $zAman) + ($muWaspada * $zWaspada) + ($muSiaga * $zSiaga) + ($muBahaya * $zBahaya);
        $den   = $muAman + $muWaspada + $muSiaga + $muBahaya;
        $score = $den > 0 ? round($num / $den, 2) : 0.0;

        $label = match (true) {
            $score >= 80 => 'BAHAYA',
            $score >= 58 => 'SIAGA',
            $score >= 33 => 'WASPADA',
            default      => 'AMAN',
        };

        return [
            'score'       => $score,
            'label'       => $label,
            'memberships' => compact('muAman', 'muWaspada', 'muSiaga', 'muBahaya'),
            'debug'       => [
                'water'    => compact('waterLow',    'waterMedium',    'waterHigh',   'waterVHigh'),
                'temp'     => compact('tempLow',     'tempMedium',     'tempHigh',    'tempVHigh'),
                'humidity' => compact('humLow',      'humMedium',      'humHigh',     'humVHigh'),
                'pressure' => compact('pressureVLow','pressureLow',    'pressureMedium','pressureHigh'),
                'wind'     => compact('windLow',     'windMedium',     'windHigh',    'windVHigh'),
                'direction'=> compact('onshore',     'crossshore',     'offshore'),
            ],
        ];
    }

    /**
     * Simpan threshold device ke cache.
     * Dipanggil dari Livewire saat user save form threshold.
     *
     * @param int   $deviceId
     * @param array $thresholds  ['sensor' => ['aman'=>x,'waspada'=>x,'siaga'=>x,'bahaya'=>x]]
     */
    public function saveThreshold(int $deviceId, array $thresholds): void
    {
        Cache::put("fuzzy_threshold_{$deviceId}", $thresholds, now()->addYear());
    }

    /**
     * Ambil threshold tersimpan untuk device, sudah merged dengan default.
     */
    public function getThreshold(int $deviceId): array
    {
        return $this->loadThreshold($deviceId);
    }

    private function tri(float $x, float $a, float $b, float $c): float
    {
        if ($x <= $a || $x >= $c) return 0.0;
        if ($x === $b)            return 1.0;
        if ($x < $b) {
            $den = $b - $a;
            return $den != 0.0 ? ($x - $a) / $den : 0.0;
        }
        $den = $c - $b;
        return $den != 0.0 ? ($c - $x) / $den : 0.0;
    }

    private function trapDesc(float $x, float $a, float $b, float $c): float
    {
        if ($x <= $a) return 1.0;
        if ($x >= $c) return 0.0;
        if ($x <= $b) return 1.0;
        $den = $c - $b;
        return $den != 0.0 ? ($c - $x) / $den : 0.0;
    }

    private function trapAsc(float $x, float $a, float $b, float $c): float
    {
        if ($x <= $a) return 0.0;
        if ($x >= $c) return 1.0;
        if ($x >= $b) return 1.0;
        $den = $b - $a;
        return $den != 0.0 ? ($x - $a) / $den : 0.0;
    }

    private function normalizeDeg(float $deg): float
    {
        $v = fmod($deg, 360.0);
        return $v < 0 ? $v + 360.0 : $v;
    }

    private function angularDistance(float $a, float $b): float
    {
        $diff = abs($this->normalizeDeg($a) - $this->normalizeDeg($b));
        return min($diff, 360 - $diff);
    }

    private function sectorMembership(float $deg, float $center, float $fullWidth = 60.0, float $fadeWidth = 120.0): float
    {
        $dist = $this->angularDistance($deg, $center);
        if ($dist <= $fullWidth / 2) return 1.0;
        if ($dist >= $fadeWidth / 2) return 0.0;
        $startFade = $fullWidth / 2;
        $endFade   = $fadeWidth / 2;
        return ($endFade - $dist) / ($endFade - $startFade);
    }

    private function onshoreMembership(float $deg): float
    {
        return $this->sectorMembership($deg, 292.5, 90, 180);
    }

    private function crossshoreMembership(float $deg): float
    {
        return max(
            $this->sectorMembership($deg, 225, 60, 140),
            $this->sectorMembership($deg, 0,   60, 140),
        );
    }

    private function offshoreMembership(float $deg): float
    {
        return $this->sectorMembership($deg, 112.5, 90, 180);
    }
}
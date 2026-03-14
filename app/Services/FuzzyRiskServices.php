<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class FuzzyRiskServices
{
    /**
     * Load threshold dari cache.
     * Fallback ke nilai BMKG Ketapang, Kalimantan Barat.
     */
    private function loadThreshold(): array
    {
        return Cache::get('fuzzy_threshold', [
            'ketinggian_air'  => ['waspada' => 130, 'bahaya' => 170],
            'suhu'            => ['waspada' => 32,  'bahaya' => 35],
            'kelembapan'      => ['waspada' => 85,  'bahaya' => 95],
            'tekanan_udara'   => ['waspada' => 1005,'bahaya' => 995],
            'kecepatan_angin' => ['waspada' => 8,   'bahaya' => 15],
            'arah_angin'      => ['waspada' => 180, 'bahaya' => 270],
        ]);
    }

    public function evaluate(array $input): array
    {
        $water     = (float) ($input['ketinggian_air']  ?? 0);
        $pressure  = (float) ($input['tekanan_udara']   ?? 0);
        $windSpeed = (float) ($input['kecepatan_angin'] ?? 0);
        $windDir   = (float) ($input['arah_angin']      ?? 0);

        // Load threshold dinamis dari cache / BMKG default
        $t = $this->loadThreshold();

        // Ketinggian Air
        $wW = (float) $t['ketinggian_air']['waspada']; // 130
        $wB = (float) $t['ketinggian_air']['bahaya'];  // 170
        $wMid = ($wW + $wB) / 2;

        $waterLow    = $this->trap($water, 0,           0,            $wW * 0.15, $wW * 0.35);
        $waterMedium = $this->tri($water,  $wW * 0.15,  $wMid,        $wB);
        $waterHigh   = $this->trap($water, $wW,         $wB,          $wB * 2,    $wB * 2);

        // Tekanan Udara (nilai rendah = bahaya)
        $pB   = (float) min($t['tekanan_udara']['waspada'], $t['tekanan_udara']['bahaya']); // 995
        $pW   = (float) max($t['tekanan_udara']['waspada'], $t['tekanan_udara']['bahaya']); // 1005
        $pMid = ($pW + $pB) / 2;

        $pressureLow    = $this->trapDesc($pressure, $pB,       $pB + 5,    $pW);
        $pressureMedium = $this->tri($pressure,      $pB,       $pMid,      $pW + 10);
        $pressureHigh   = $this->trapAsc($pressure,  $pW,       $pW + 5,    $pW + 20);

        // Kecepatan Angin
        $vW   = (float) $t['kecepatan_angin']['waspada']; // 8
        $vB   = (float) $t['kecepatan_angin']['bahaya'];  // 15
        $vMid = ($vW + $vB) / 2;

        $windLow    = $this->trapDesc($windSpeed, 0,    0,           $vW * 0.25, $vW * 0.5);
        $windMedium = $this->tri($windSpeed,      $vW * 0.25, $vMid, $vB);
        $windHigh   = $this->trapAsc($windSpeed,  $vW,  $vB,         $vB * 2,    $vB * 2);

        // Arah Angin
        $onshore    = $this->onshoreMembership($windDir);
        $crossshore = $this->crossshoreMembership($windDir);
        $offshore   = $this->offshoreMembership($windDir);

        // Aturan Fuzzy
        $aman    = [];
        $waspada = [];
        $siaga   = [];
        $bahaya  = [];

        $aman[] = min($waterLow, $windLow, max($pressureMedium, $pressureHigh));
        $aman[] = min($waterLow, $offshore);

        $waspada[] = min($waterMedium, $windLow);
        $waspada[] = min($waterLow, $windMedium, $onshore);
        $waspada[] = min($waterMedium, $offshore);

        $siaga[] = min($waterMedium, $windMedium, $onshore);
        $siaga[] = min($waterHigh, $windLow);
        $siaga[] = min($waterMedium, $pressureLow, $onshore);
        $siaga[] = min($waterMedium, $windHigh, $crossshore);

        $bahaya[] = min($waterHigh, $windMedium, $onshore);
        $bahaya[] = min($waterHigh, $windHigh);
        $bahaya[] = min($waterHigh, $pressureLow);
        $bahaya[] = min($waterHigh, $windHigh, $onshore);
        $bahaya[] = min($waterMedium, $windHigh, $pressureLow, $onshore);

        $muAman    = max($aman    ?: [0]);
        $muWaspada = max($waspada ?: [0]);
        $muSiaga   = max($siaga   ?: [0]);
        $muBahaya  = max($bahaya  ?: [0]);

        // Defuzzifikasi (Weighted Average / Sugeno)
        $zAman    = 20;
        $zWaspada = 45;
        $zSiaga   = 70;
        $zBahaya  = 95;

        $numerator   = ($muAman * $zAman) + ($muWaspada * $zWaspada) + ($muSiaga * $zSiaga) + ($muBahaya * $zBahaya);
        $denominator = $muAman + $muWaspada + $muSiaga + $muBahaya;

        $score = $denominator > 0 ? round($numerator / $denominator, 2) : 0.0;

        $label = match (true) {
            $score >= 85 => 'BAHAYA',
            $score >= 65 => 'SIAGA',
            $score >= 40 => 'WASPADA',
            default      => 'AMAN',
        };

        return [
            'score'       => $score,
            'label'       => $label,
            'memberships' => [
                'aman'    => round($muAman,    4),
                'waspada' => round($muWaspada, 4),
                'siaga'   => round($muSiaga,   4),
                'bahaya'  => round($muBahaya,  4),
            ],
            'debug' => [
                'water'     => compact('waterLow',    'waterMedium',    'waterHigh'),
                'pressure'  => compact('pressureLow', 'pressureMedium', 'pressureHigh'),
                'wind'      => compact('windLow',     'windMedium',     'windHigh'),
                'direction' => compact('onshore',     'crossshore',     'offshore'),
            ],
        ];
    }

    private function tri(float $x, float $a, float $b, float $c): float
    {
        if ($a === $b && $x === $a) return $x <= $b ? 1.0 : 0.0;
        if ($b === $c && $x === $c) return $x >= $b ? 1.0 : 0.0;
        if ($x <= $a || $x >= $c)  return 0.0;
        if ($x === $b)              return 1.0;

        if ($x < $b) {
            $den = $b - $a;
            return $den != 0.0 ? ($x - $a) / $den : 0.0;
        }

        $den = $c - $b;
        return $den != 0.0 ? ($c - $x) / $den : 0.0;
    }

    private function trap(float $x, float $a, float $b, float $c, float $d): float
    {
        if ($x <= $a || $x >= $d) return 0.0;
        if ($x >= $b && $x <= $c) return 1.0;

        if ($x > $a && $x < $b) {
            $den = $b - $a;
            return $den != 0.0 ? ($x - $a) / $den : 0.0;
        }

        $den = $d - $c;
        return $den != 0.0 ? ($d - $x) / $den : 0.0;
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

        if ($dist <= $fullWidth / 2)  return 1.0;
        if ($dist >= $fadeWidth / 2)  return 0.0;

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
        $a = $this->sectorMembership($deg, 225, 60, 140);
        $b = $this->sectorMembership($deg, 0,   60, 140);
        return max($a, $b);
    }

    private function offshoreMembership(float $deg): float
    {
        return $this->sectorMembership($deg, 112.5, 90, 180);
    }
}
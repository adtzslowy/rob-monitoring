<?php

namespace App\Services;

class FuzzyRiskServices 
{
    public function evaluate(array $input)
    {
        $water = (float) ($input['ketinggian_air'] ?? 0);
        $pressure = (float) ($input['tekanan_udara'] ?? 0);
        $windSpeed = (float) ($input['kecepatan_angin'] ?? 0);
        $windDir = (float) ($input['arah_angin'] ?? 0);

        // Fuzzy implementation
        $waterLow = $this->trap($water, 0, 0, 20, 45);
        $waterMedium = $this->tri($water, 30, 70, 110);
        $waterHigh = $this->trap($water, 90, 120, 300, 300);

        $pressureLow = $this->trapDesc($pressure, 980, 995, 1005);
        $pressureMedium = $this->tri($pressure, 1000, 1010, 1020);
        $pressureHigh = $this->trapAsc($pressure, 1015, 1022, 1040);

        $windLow = $this->trapDesc($windSpeed, 0, 0, 2, 5);
        $windMedium = $this->tri($windSpeed, 4, 8, 12);
        $windHigh = $this->trapAsc($windSpeed, 10, 14, 40, 40);

        $onshore = $this->onshoreMembership($windDir);
        $crossshore = $this->crossshoreMembership($windDir);
        $offshore = $this->offshoreMembership($windDir);

        // Base aturan fuzzy
        $aman = [];
        $waspada = [];
        $siaga = [];
        $bahaya = [];

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

        $muAman = max($aman ?: [0]);
        $muWaspada = max($waspada ?: [0]);
        $muSiaga = max($siaga ?: [0]);
        $muBahaya = max($bahaya ?: [0]);

        // defuzzification method
        $zAman = 20;
        $zWaspada = 45;
        $zSiaga = 70;
        $zBahaya = 95;

        $numerator = ($muAman * $zAman) + ($muWaspada * $zWaspada) + ($muSiaga * $zSiaga) + ($muBahaya * $zBahaya);
        $denominator = $muAman + $muWaspada + $muSiaga + $muBahaya;
        
        $score = $denominator > 0 ? round($numerator / $denominator, 2) : 0.0;

        $label = match(true) {
            $score >= 85 => 'BAHAYA',
            $score >= 65 => 'SIAGA',
            $score >= 40 => 'WASPADA',
            default => 'AMAN',
        };

        return [
            'score' => $score,
            'label' => $label,
            'memberships' => [
                'aman' => round($muAman, 4),
                'waspada' => round($muWaspada, 4),
                'siaga' => round($muSiaga, 4),
                'bahaya' => round($muBahaya, 4),
            ],
            'debug' => [
                'water' => compact('waterLow', 'waterMedium', 'waterHigh'),
                'pressure' => compact('pressureLow', 'pressureMedium', 'pressureHigh'),
                'wind' => compact('windLow', 'windMedium', 'windHigh'),
                'direction' => compact('onshore', 'crossshore', 'offshore'),
            ],
        ];
    }

    private function tri(float $x, float $a, float $b, float $c)
    {
        if ($a === $b && $x === $a) {
            return $x <= $b ? 1.0 : 0.0;
        }

        if ($b === $c && $x === $c) {
            return $x >= $b ? 1.0 : 0.0;
        }

        if ($x <= $a || $x >= $c) {
            return 0.0;
        }

        if ($x === $b) {
            return 1.0;
        }

        if ($x < $b) {
            $den = ($b - $a);
            return $den != 0.0 ? ($x - $a) / $den : 0.0;
        }

        $den = ($c - $b);
        return $den != 0.0 ? ($c - $x) / $den : 0.0;
    }

    private function trap(float $x, float $a, float $b, float $c, float $d): float
    {
        if ($x <= $a || $x >= $d) {
            return 0.0;
        }

        if ($x >= $b && $x <= $c) {
            return 1.0;
        }

        if ($x > $a && $x < $b) {
            $den = ($b - $a);
            return $den != 0.0 ? ($x - $a) / $den : 0.0;
        }

        $den = ($d - $c);
        return $den != 0.0 ? ($d - $x) / $den : 0.0;
    }

    private function trapDesc(float $x, float $a, float $b, float $c): float
    {
        if ($x <= $a) {
            return 1.0;
        }

        if ($x >= $c) {
            return 0.0;
        }

        if ($x <= $b) {
            return 1.0;
        }

        $den = ($c - $b);
        return $den != 0.0 ? ($c - $x) / $den : 0.0;
    }

    // Nol di kiri, besar di kanan
    private function trapAsc(float $x, float $a, float $b, float $c): float
    {
        if ($x <= $a) {
            return 0.0;
        }

        if ($x >= $c) {
            return 1.0;
        }

        if ($x >= $b) {
            return 1.0;
        }

        $den = ($b - $a);
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

        if ($dist <= ($fullWidth / 2)) {
            return 1.0;
        }

        if ($dist >= ($fadeWidth / 2)) {
            return 0.0;
        }

        $startFade = $fullWidth / 2;
        $endFade = $fadeWidth / 2;
        return ($endFade - $dist) / ($endFade - $startFade);
    }

    private function onshoreMembership(float $deg): float
    {
        // Dummy pusat sektor onshore di 292.5° (WNW)
        return $this->sectorMembership($deg, 292.5, 90, 180);
    }

    private function crossshoreMembership(float $deg): float
    {
        $a = $this->sectorMembership($deg, 225, 60, 140);
        $b = $this->sectorMembership($deg, 0, 60, 140);
        return max($a, $b);
    }

    private function offshoreMembership(float $deg): float
    {
        // Berlawanan dari onshore
        return $this->sectorMembership($deg, 112.5, 90, 180);
    }
}
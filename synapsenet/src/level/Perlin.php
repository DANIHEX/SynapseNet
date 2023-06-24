<?php

namespace synapsenet\level;

class Perlin {
    private $p;

    public function __construct() {
        $this->p = [];
        for ($i = 0; $i < 512; ++$i) {
            $this->p[$i] = perlinPermutation[$i % 256];
        }
    }

    private function dot($ax, $bx, $ay, $by, $az, $bz) {
        return $ax * $bx + $ay * $by + $az * $bz;
    }

    private function grad($hash, $x, $y, $z) {
        $gradientVector = perlinGradientVectors[$hash & 0x0f];
        return $this->dot($gradientVector[0], $gradientVector[1], $gradientVector[2], $x, $y, $z);
    }

    private function fade($t) {
        return 6 * pow($t, 5) - 15 * pow($t, 4) + 10 * pow($t, 3);
    }

    private function lerp($v0, $v1, $t) {
        return $v0 + $t * ($v1 - $v0);
    }

    public function noise($x, $y, $z) {
        $xi = floor($x) & 255;
        $yi = floor($y) & 255;
        $zi = floor($z) & 255;
        $xf = $x - floor($x);
        $yf = $y - floor($y);
        $zf = $z - floor($z);

        $aaa = $this->p[$this->p[$this->p[$xi] + $yi] + $zi];
        $aba = $this->p[$this->p[$this->p[$xi] + $yi + 1] + $zi];
        $aab = $this->p[$this->p[$this->p[$xi] + $yi] + $zi + 1];
        $abb = $this->p[$this->p[$this->p[$xi] + $yi + 1] + $zi + 1];
        $baa = $this->p[$this->p[$this->p[$xi + 1] + $yi] + $zi];
        $bba = $this->p[$this->p[$this->p[$xi + 1] + $yi + 1] + $zi];
        $bab = $this->p[$this->p[$this->p[$xi + 1] + $yi] + $zi + 1];
        $bbb = $this->p[$this->p[$this->p[$xi + 1] + $yi + 1] + $zi + 1];

        $u = $this->fade($xf);
        $v = $this->fade($yf);
        $w = $this->fade($zf);

        $x1 = $this->lerp($this->grad($aaa, $xf, $yf, $zf), $this->grad($baa, $xf - 1, $yf, $zf), $u);
        $x2 = $this->lerp($this->grad($aba, $xf, $yf - 1, $zf), $this->grad($bba, $xf - 1, $yf - 1, $zf), $u);
        $y1 = $this->lerp($x1, $x2, $v);
        $x1 = $this->lerp($this->grad($aab, $xf, $yf, $zf - 1), $this->grad($bab, $xf - 1, $yf, $zf - 1), $u);
        $x2 = $this->lerp($this->grad($abb, $xf, $yf - 1, $zf - 1), $this->grad($bbb, $xf - 1, $yf - 1, $zf - 1), $u);
        $y2 = $this->lerp($x1, $x2, $v);

        return $this->lerp($y1, $y2, $w);
    }

    public function octaveNoise($x, $y, $z, $octaves = 1, $persistence = 0.2, $lacunarity = 2) {
        $total = 0;
        $frequency = 1;
        $amplitude = 1;
        $maxValue = 0;

        for ($i = 0; $i < $octaves; ++$i) {
            $total += $this->noise($x * $frequency, $y * $frequency, $z * $frequency) * $amplitude;

            $maxValue += $amplitude;

            $amplitude *= $persistence;
            $frequency *= $lacunarity;
        }

        return $total / $maxValue;
    }

    public function perlin($x, $y, $z, $r = 1, $scale = 1, $octaves = 1, $persistence = 0.2, $lacunarity = 2) {
        $frequency = 1;
        $amplitude = 1;
        $height = 0;

        for ($i = 0; $i < $octaves; ++$i) {
            $sampleX = ($x / $scale) * $frequency * ($i + 1);
            $sampleY = ($y / $scale) * $frequency * ($i + 1);
            $sampleZ = ($z / $scale) * $frequency * ($i + 1);
            $total = 0;

            for ($j = 0; $j < 14; ++$j) {
                $val = $perlins[$j];
                $s = $val[0];
                $h = $val[1];
                $total += $this->noise($sampleX * $s, $sampleY * $s, $sampleZ * $s) * $amplitude * $h;
            }

            $total *= ($avg * 1) / ($i + 1);
            $height += $total;

            $amplitude *= $persistence;
            $frequency *= $lacunarity;
        }

        return $height;
    }
}

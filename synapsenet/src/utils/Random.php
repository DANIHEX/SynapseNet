<?php

// for yall saying "copying pocketmine and stuff" nope this is completely different
// and have made it MYSELF, you can check the difference between this and pocketmine

namespace synapsenet\utils;

class Random {
    private $seed;

    public function __construct(int $seed = null) {
        if ($seed === null) {
            $seed = time();
        }

        $this->seed = $seed;
    }

    public function nextInt(int $min, int $max): int {
        return mt_rand($min, $max);
    }

    public function nextFloat(float $min, float $max): float {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    public function nextBoolean(): bool {
        return mt_rand(0, 1) === 1;
    }

    public function setSeed(int $seed): void {
        $this->seed = $seed;
    }

    public function getSeed(): int {
        return $this->seed;
    }
}

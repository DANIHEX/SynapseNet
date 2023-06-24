<?php

namespace synapsenet\level;

class Generator {
    protected $generatorName;
    protected $blockStatesMap;

    public function __construct($blockStatesMap) {
        $this->blockStatesMap = $blockStatesMap;
    }

    public function generate($x, $z) {
        $chunk = [];
        for ($i = 0; $i < 16; $i++) {
            for ($j = 0; $j < 16; $j++) {
                for ($k = 0; $k < 256; $k++) {
                    $blockRuntimeID = $this->getBlockRuntimeID($x + $i, $k, $z + $j);
                    $chunk[$i][$k][$j] = $blockRuntimeID;
                }
            }
        }

        return $chunk;
    }

    protected function getBlockRuntimeID($x, $y, $z) {
        if ($y < 64) {
            return 1; // Use block runtime ID 1 for blocks below y = 64 right????
        } else {
            return 2; // Use block runtime ID 2 for blocks above or at y = 64 right?
        }
    }
}

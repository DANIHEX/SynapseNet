<?php

namespace synapsenet\level\chunk;

class BlockStorage {
    private $blocks;
    private $palette;

    public function __construct($runtimeID) {
        $this->blocks = array_fill(0, 4096, 0);
        $this->palette = [$runtimeID];
    }

    public function getBlockRuntimeID($x, $y, $z) {
        return $this->palette[$this->blocks[($x << 8) | ($z << 4) | $y]];
    }

    public function setBlockRuntimeID($x, $y, $z, $runtimeID) {
        if (!in_array($runtimeID, $this->palette)) {
            $this->palette[] = $runtimeID;
        }
        $this->blocks[($x << 8) | ($z << 4) | $y] = array_search($runtimeID, $this->palette);
    }

    public function getHighestBlockAt($x, $z) {
        for ($y = 15; $y >= 0; --$y) {
            if ($this->blocks[($x << 8) | ($z << 4) | $y] != 0) {
                return $y;
            }
        }
        return -1;
    }

    public function isEmpty() {
        if (count($this->palette) <= 1) {
            return true;
        }
        foreach ($this->blocks as $block) {
            if ($block != 0) {
                return false;
            }
        }
        return true;
    }
}

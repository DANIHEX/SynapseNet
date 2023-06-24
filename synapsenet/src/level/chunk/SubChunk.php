<?php

namespace synapsenet\level\chunk;

class SubChunk {
    private $blockStorages;

    public function __construct($runtimeID) {
        $this->blockStorages = [new BlockStorage($runtimeID), new BlockStorage($runtimeID)];
    }

    public function getBlockRuntimeID($x, $y, $z, $layer) {
        return $this->blockStorages[$layer]->getBlockRuntimeID($x, $y, $z);
    }

    public function setBlockRuntimeID($x, $y, $z, $layer, $runtimeID) {
        $this->blockStorages[$layer]->setBlockRuntimeID($x, $y, $z, $runtimeID);
    }

    public function getHighestBlockAt($x, $z, $layer) {
        return $this->blockStorages[$layer]->getHighestBlockAt($x, $z);
    }

    public function isEmpty() {
        foreach ($this->blockStorages as $blockStorage) {
            if (!$blockStorage->isEmpty()) {
                return false;
            }
        }
        return true;
    }
}

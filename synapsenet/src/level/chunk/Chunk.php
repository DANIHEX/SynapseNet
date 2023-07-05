<?php

namespace synapsenet\level\chunk;

class Chunk {
    private $x;
    private $z;
    private $subChunks;
    private $biomes;
    private $runtimeID;
    private const MAXSUBCHUNKS = 24;

    public function __construct($x, $z, $runtimeID) {
        $this->x = $x;
        $this->z = $z;
        $this->runtimeID = $runtimeID;
        $this->subChunks = new \SplObjectStorage();
        $this->biomes = array_fill(0, self::MAXSUBCHUNKS, new BlockStorage(1));
    }

    public function getBlockRuntimeID($x, $y, $z, $layer) {
        $index = $y >> 4;
        if ($this->subChunks->contains($index)) {
            $subChunk = $this->subChunks[$index];
            return $subChunk->getBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, $layer);
        }
        return $this->runtimeID;
    }

    public function setBlockRuntimeID($x, $y, $z, $layer, $runtimeID) {
        $index = $y >> 4;
        if ($index < self::MAXSUBCHUNKS && $index >= 0) {
            if (!$this->subChunks->contains($index)) {
                $this->subChunks[$index] = new SubChunk($this->runtimeID);
            }
            $subChunk = $this->subChunks[$index];
            return $subChunk->setBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, $layer, $runtimeID);
        }
    }

    public function getHighestBlockAt($x, $z, $layer) {
        for ($i = self::MAXSUBCHUNKS - 1; $i >= 0; --$i) {
            if ($this->subChunks->contains($i)) {
                $subChunk = $this->subChunks[$i];
                $y = $subChunk->getHighestBlockAt($x, $z, $layer);
                if ($y != -1) {
                    return ($i << 4) + $y;
                }
            }
        }
        return -1;
    }

    public function isEmpty() {
        if ($this->subChunks->count() == 0) {
            return true;
        }
        for ($i = 0; $i < self::MAXSUBCHUNKS; ++$i) {
            if ($this->subChunks->contains($i)) {
                $subChunk = $this->subChunks[$i];
                if (!$subChunk->isEmpty()) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getSubChunksSendCount() {
        for ($i = self::MAXSUBCHUNKS - 1; $i >= 0; --$i) {
            if ($this->subChunks->contains($i)) {
                $subChunk = $this->subChunks[$i];
                if (!$subChunk->isEmpty()) {
                    return $i + 1;
                }
            }
        }
        return 0;
    }

    public function getBiome($x, $z) {
        $index = ($z << 4) | $x;
        return $this->biomes[$index];
    }

    public function setBiome($x, $z, $biomeId) {
        $index = ($z << 4) | $x;
        $this->biomes[$index] = $biomeId;
    }

    public function getBlock($x, $y, $z) {
        $index = $y >> 4;
        if ($this->subChunks->contains($index)) {
            $subChunk = $this->subChunks[$index];
            return $subChunk->getBlock($x & 0x0f, $y & 0x0f, $z & 0x0f);
        }
        return $this->runtimeID;
    }

    public function setBlock($x, $y, $z, $blockId, $meta = 0) {
        $index = $y >> 4;
        if ($index < self::MAXSUBCHUNKS && $index >= 0) {
            if (!$this->subChunks->contains($index)) {
                $this->subChunks[$index] = new SubChunk($this->runtimeID);
            }
            $subChunk = $this->subChunks[$index];
            return $subChunk->setBlock($x & 0x0f, $y & 0x0f, $z & 0x0f, $blockId, $meta);
        }
    }

    public function getChunkX() {
        return $this->x;
    }

    public function getChunkZ() {
        return $this->z;
    }

    public function getSubChunk($y) {
        if ($this->subChunks->contains($y)) {
            return $this->subChunks[$y];
        }
        return null;
    }

    public function setSubChunk($y, SubChunk $subChunk) {
        $this->subChunks[$y] = $subChunk;
    }

    public function removeSubChunk($y) {
        if ($this->subChunks->contains($y)) {
            unset($this->subChunks[$y]);
        }
    }

    public function getBiomes() {
        return $this->biomes;
    }

    public function setBiomes(array $biomes) {
        $this->biomes = $biomes;
    }

    public function getRuntimeID() {
        return $this->runtimeID;
    }

    public function setRuntimeID($runtimeID) {
        $this->runtimeID = $runtimeID;
    }

    public function setBlockId($x, $y, $z, $blockId) {
        $index = $y >> 4;
        if ($index < self::MAXSUBCHUNKS && $index >= 0) {
            if (!$this->subChunks->contains($index)) {
                $this->subChunks[$index] = new SubChunk($this->runtimeID);
            }
            $subChunk = $this->subChunks[$index];
            return $subChunk->setBlockId($x & 0x0f, $y & 0x0f, $z & 0x0f, $blockId);
        }
    }

    public function getBlockId($x, $y, $z) {
        $index = $y >> 4;
        if ($this->subChunks->contains($index)) {
            $subChunk = $this->subChunks[$index];
            return $subChunk->getBlockId($x & 0x0f, $y & 0x0f, $z & 0x0f);
        }
        return $this->runtimeID;
    }

    public function setBlockData($x, $y, $z, $data) {
        $index = $y >> 4;
        if ($index < self::MAXSUBCHUNKS && $index >= 0) {
            if (!$this->subChunks->contains($index)) {
                $this->subChunks[$index] = new SubChunk($this->runtimeID);
            }
            $subChunk = $this->subChunks[$index];
            return $subChunk->setBlockData($x & 0x0f, $y & 0x0f, $z & 0x0f, $data);
        }
    }

    public function getBlockData($x, $y, $z) {
        $index = $y >> 4;
        if ($this->subChunks->contains($index)) {
            $subChunk = $this->subChunks[$index];
            return $subChunk->getBlockData($x & 0x0f, $y & 0x0f, $z & 0x0f);
        }
        return 0;
    }
}
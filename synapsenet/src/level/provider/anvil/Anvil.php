<?php

namespace synapsenet\level\provider\anvil;

use synapsenet\level\CoordinateUtils;
use synapsenet\level\Chunk;

class Anvil {
    /**
     * Path to the world folder
     * @var string
     */
    private $path;
    /**
     * Holds all loaded regions
     * @var array
     */
    private $regions;
    /**
     * Holds all loaded chunks
     * @var array
     */
    private $chunks;

    /**
     * Loads up a world
     * @param string $path
     */
    public function __construct($path) {
        $this->path = $path;
        $this->regions = array();
        $this->chunks = array();
    }

    /**
     * Loads a region
     * @param int $x
     * @param int $z
     * @return Region
     */
    public function loadRegion($x, $z) {
        $xz = CoordinateUtils::hashXZ($x, $z);
        if (!isset($this->regions[$xz])) {
            $region = new Region("{$this->path}/region/r.{$x}.{$z}.mca");
            $this->regions[$xz] = $region;
            return $region;
        }
        return $this->regions[$xz];
    }

    /**
     * Loads a chunk
     * @param int $x
     * @param int $z
     * @return Chunk
     */
    public function loadChunk($x, $z) {
        $xz = CoordinateUtils::hashXZ($x, $z);
        if (!isset($this->chunks[$xz])) {
            $region = $this->loadRegion($x >> 5, $z >> 5);
            $data = $region->readChunkData($x & 31, $z & 31);
            $chunk = new Chunk($x, $z);
            $chunk->loadChunkData($data);
            $this->chunks[$xz] = $chunk;
            return $chunk;
        }
        return $this->chunks[$xz];
    }
}

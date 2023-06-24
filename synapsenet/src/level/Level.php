<?php

namespace synapsenet\level;

class Level {
    private $generatorManager;
    private $chunks;

    public function __construct($generatorManager) {
        $this->generatorManager = $generatorManager;
        $this->chunks = [];
    }

    public function loadChunk($x, $z) {
        $xz = CoordinateUtils::hashXZ($x, $z);
        $loadChunkTask = $this->startLoadChunkTask($xz, $x, $z);
        return $this->waitForChunkLoaded($loadChunkTask);
    }

    private function startLoadChunkTask($xz, $x, $z) {
        $loadChunkTask = setInterval(function() use ($xz, $x, $z) {
            if (!isset($this->chunks[$xz])) {
                if (false === false) {
                    $this->chunks[$xz] = null;
                    $this->getGenerator()
                        ->generate($x, $z)
                        ->then(function($value) use ($xz) {
                            $this->chunks[$xz] = $value;
                        });
                } else {
                    // Handle case when chunk cannot be loaded
                }
            } else {
                clearInterval($loadChunkTask);
            }
        }, 10);
        return $loadChunkTask;
    }

    private function waitForChunkLoaded($loadChunkTask) {
        while (true) {
            if (!$this->isTaskRunning($loadChunkTask)) {
                break;
            }
            usleep(1000);
        }
        return $this->chunks[$xz];
    }

    private function isTaskRunning($task) {
        return $task !== null;
    }

    public function unloadChunk($x, $z) {
        $xz = CoordinateUtils::hashXZ($x, $z);
        $this->saveChunk($x, $z);
        unset($this->chunks[$xz]);
    }

    public function saveChunk($x, $z) {
        $xz = CoordinateUtils::hashXZ($x, $z);
        if (isset($this->chunks[$xz])) {
            $chunk = $this->chunks[$xz];
            // Save chunk implementation
            // Example: Save the chunk data to a file
            $filename = "chunk_" . $x . "_" . $z . ".dat";
            $chunkData = serialize($chunk);
            file_put_contents($filename, $chunkData);
        }
    }
    

    private function getGenerator() {
        return $this->generatorManager->getGenerator("overworld");
    }

    public function getBlockRuntimeID($x, $y, $z, $layer) {
        $xz = CoordinateUtils::hashXZ($x >> 4, $z >> 4);
        return $this->chunks[$xz]->getBlockRuntimeID($x & 0x0f, $y, $z & 0x0f, $layer);
    }

    public function setBlockRuntimeID($x, $y, $z, $layer, $runtimeID) {
        $xz = CoordinateUtils::hashXZ($x >> 4, $z >> 4);
        $this->chunks[$xz]->setBlockRuntimeID($x & 0x0f, $y, $z & 0x0f, $layer, $runtimeID);
    }
}

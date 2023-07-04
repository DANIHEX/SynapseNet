<?php

namespace synapsenet\level\structures;

use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;
use synapsenet\level\Chunk;

class BirchTree {
    public static function generate($x, $y, $z, Chunk $chunk) {
        $log = BlockFactory::get(BlockIds::BIRCH_LOG);
        $leaves = BlockFactory::get(BlockIds::BIRCH_LEAVES);

        $trunkHeight = mt_rand(5, 7);
        $leafStart = $y + $trunkHeight - 2;

        // Generate trunk
        for ($i = $y; $i < $y + $trunkHeight; $i++) {
            $chunk->setBlockId($x, $i, $z, BlockIds::BIRCH_LOG);
            $chunk->setBlockData($x, $i, $z, 0);
        }

        // Generate leaves
        for ($leafY = $leafStart; $leafY <= $y + $trunkHeight; $leafY++) {
            $leafRadius = $leafY - ($y + $trunkHeight) + 1;
            $leafRadiusSq = $leafRadius * $leafRadius;

            for ($leafX = $x - 2; $leafX <= $x + 2; $leafX++) {
                for ($leafZ = $z - 2; $leafZ <= $z + 2; $leafZ++) {
                    $distSq = ($leafX - $x) * ($leafX - $x) + ($leafZ - $z) * ($leafZ - $z);

                    if ($distSq <= $leafRadiusSq) {
                        $chunk->setBlockId($leafX, $leafY, $leafZ, BlockIds::BIRCH_LEAVES);
                        $chunk->setBlockData($leafX, $leafY, $leafZ, 0);
                    }
                }
            }
        }

        // Generate top leaves
        for ($leafY = $y + $trunkHeight + 1; $leafY <= $y + $trunkHeight + 2; $leafY++) {
            for ($leafX = $x - 1; $leafX <= $x + 1; $leafX++) {
                for ($leafZ = $z - 1; $leafZ <= $z + 1; $leafZ++) {
                    $chunk->setBlockId($leafX, $leafY, $leafZ, BlockIds::BIRCH_LEAVES);
                    $chunk->setBlockData($leafX, $leafY, $leafZ, 0);
                }
            }
        }
    }
}

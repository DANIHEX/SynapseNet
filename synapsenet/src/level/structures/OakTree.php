<?php

namespace synapsenet\level\structures;

use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;
use synapsenet\level\Chunk;

class OakTree {
    public static function generate($x, $y, $z, Chunk $chunk) {
        $log = BlockFactory::get(BlockIds::OAK_LOG);
        $leaves = BlockFactory::get(BlockIds::OAK_LEAVES);

        $trunkHeight = mt_rand(4, 7);
        $leafStart = $y + $trunkHeight - 3;

        // Generate trunk
        for ($i = $y; $i < $y + $trunkHeight; $i++) {
            $chunk->setBlockId($x, $i, $z, BlockIds::OAK_LOG);
            $chunk->setBlockData($x, $i, $z, 0);
        }

        // Generate leaves
        for ($leafY = $leafStart; $leafY <= $y + $trunkHeight; $leafY++) {
            $leafRadius = $leafY - ($y + $trunkHeight);
            $leafRadiusSq = $leafRadius * $leafRadius;

            for ($leafX = $x - 2; $leafX <= $x + 2; $leafX++) {
                for ($leafZ = $z - 2; $leafZ <= $z + 2; $leafZ++) {
                    $distSq = ($leafX - $x) * ($leafX - $x) + ($leafZ - $z) * ($leafZ - $z);

                    if ($distSq <= $leafRadiusSq || ($leafRadius > 0 && $distSq <= ($leafRadius + 1) * ($leafRadius + 1))) {
                        $chunk->setBlockId($leafX, $leafY, $leafZ, BlockIds::OAK_LEAVES);
                        $chunk->setBlockData($leafX, $leafY, $leafZ, 0);
                    }
                }
            }
        }
    }
}

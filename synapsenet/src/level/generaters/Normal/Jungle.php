<?php

namespace synapsenet\level\generators\Normal;

// needs improved


use synapsenet\level\Biome;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;

class Jungle {
    public static function generator($x, $y, $z, $chunkX, $chunkZ, Chunk $chunk, $blockMap, Random $random) {
        $grass = BlockFactory::createBlock(BlockIds::GRASS);
        $oakLog = BlockFactory::createBlock(BlockIds::OAK_LOG);
        $oakLeaves = BlockFactory::createBlock(BlockIds::OAK_LEAVES);
        $bamboo = BlockFactory::createBlock(BlockIds::BAMBOO);
        
        $chunk->getBiome($y >> 4)->setBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, Biome::JUNGLE);

        if ($y === 0) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, BlockFactory::get(BlockIds::BEDROCK));
        } elseif ($y < 4) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, BlockFactory::get(BlockIds::STONE));
        } elseif ($y < 128) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $grass);

            if ($y === 4) {
                // Generate tree
                if (mt_rand(1, 10) === 1) {
                    self::generateTree($x, $y + 1, $z, $chunk, $oakLog, $oakLeaves);
                }
            }

            // Generate bamboo in bamboo jungle variant
            if ($chunk->getBiomeId($x, $z) === Biome::BAMBOO_JUNGLE) {
                if (mt_rand(1, 30) === 1) {
                    $chunk->setBlockRuntimeID($x, $y + 1, $z, 0, $bamboo);
                }
            }
        }
    }

    private static function generateTree($x, $y, $z, Chunk $chunk, $logBlock, $leavesBlock) {
        // Generate trunk
        for ($i = $y; $i < $y + 7; $i++) {
            $chunk->setBlockRuntimeID($x, $i, $z, 0, $logBlock);
        }

        // Generate leaves
        for ($leafY = $y + 3; $leafY <= $y + 7; $leafY++) {
            $leafRadius = $leafY - ($y + 7);
            $leafRadiusSq = $leafRadius * $leafRadius;

            for ($leafX = $x - 3; $leafX <= $x + 3; $leafX++) {
                for ($leafZ = $z - 3; $leafZ <= $z + 3; $leafZ++) {
                    $distSq = ($leafX - $x) * ($leafX - $x) + ($leafZ - $z) * ($leafZ - $z);

                    if ($distSq <= $leafRadiusSq || ($leafRadius > 0 && $distSq <= ($leafRadius + 1) * ($leafRadius + 1))) {
                        $chunk->setBlockRuntimeID($leafX, $leafY, $leafZ, 0, $leavesBlock);
                    }
                }
            }
        }
    }
}

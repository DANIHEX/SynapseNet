<?php

namespace synapsenet\level\generators\Normal;

// nees to be improved

use synapsenet\level\Biome;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;
use synapsenet\level\Perlin;

class Mountains {
    public static function generator($x, $y, $z, $chunkX, $chunkZ, Chunk $chunk, $blockMap) {
        $stone = BlockFactory::createBlock(BlockIds::STONE);
        $dirt = BlockFactory::createBlock(BlockIds::DIRT);
        $grass = BlockFactory::createBlock(BlockIds::GRASS);
        $oakLog = BlockFactory::createBlock(BlockIds::OAK_LOG);
        $oakLeaves = BlockFactory::createBlock(BlockIds::OAK_LEAVES);
        $emeraldOre = BlockFactory::createBlock(BlockIds::EMERALD_ORE);
        $gravel = BlockFactory::createBlock(BlockIds::GRAVEL);

        $chunk->getBiome($y >> 4)->setBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, Biome::MOUNTAINS);

        if ($y === 0) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, BlockFactory::get(BlockIds::BEDROCK));
        } elseif ($y < 4) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $stone);
        } elseif ($y < 128) {
            $perlin = new Perlin();
            $noise = $perlin->noise(($chunkX << 4) + $x + 0.5, $y + 0.5, ($chunkZ << 4) + $z + 0.5, 8, 3);

            if ($noise < 0) {
                $chunk->setBlockRuntimeID($x, $y, $z, 0, $grass);
            } else {
                $chunk->setBlockRuntimeID($x, $y, $z, 0, $dirt);
            }

            if ($y === 4 && $noise >= 0) {
                // Generate tree
                if (mt_rand(1, 10) === 1) {
                    self::generateTree($x, $y + 1, $z, $chunk, $oakLog, $oakLeaves);
                }
            }

            // Generate emerald ore
            if ($noise >= 0 && mt_rand(1, 30) === 1) {
                $chunk->setBlockRuntimeID($x, $y, $z, 0, $emeraldOre);
            }

            // Generate other ores
            if ($y < 40) {
                // Generate coal ore
                if (mt_rand(1, 20) === 1) {
                    $chunk->setBlockRuntimeID($x, $y, $z, 0, BlockFactory::get(BlockIds::COAL_ORE));
                }
            } elseif ($y < 20) {
                // Generate iron ore
                if (mt_rand(1, 15) === 1) {
                    $chunk->setBlockRuntimeID($x, $y, $z, 0, BlockFactory::get(BlockIds::IRON_ORE));
                }
            } elseif ($y < 10) {
                // Generate gold ore
                if (mt_rand(1, 10) === 1) {
                    $chunk->setBlockRuntimeID($x, $y, $z, 0, BlockFactory::get(BlockIds::GOLD_ORE));
                }
            } elseif ($y < 5) {
                // Generate diamond ore
                if (mt_rand(1, 8) === 1) {
                    $chunk->setBlockRuntimeID($x, $y, $z, 0, BlockFactory::get(BlockIds::DIAMOND_ORE));
                }
            }
        } else {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, BlockFactory::get(BlockIds::AIR));
        }
    }

    public static function generateTree($x, $y, $z, Chunk $chunk, $logBlock, $leavesBlock) {
        // Generate trunk
        for ($trunkY = $y; $trunkY <= $y + 3; $trunkY++) {
            $chunk->setBlockRuntimeID($x, $trunkY, $z, 0, $logBlock);
        }

        // Generate leaves
        for ($leafY = $y + 3; $leafY <= $y + 6; $leafY++) {
            $leafRadius = $leafY - ($y + 6);
            $leafRadiusSq = $leafRadius * $leafRadius;

            for ($leafX = $x - 1; $leafX <= $x + 1; $leafX++) {
                for ($leafZ = $z - 1; $leafZ <= $z + 1; $leafZ++) {
                    $distSq = ($leafX - $x) * ($leafX - $x) + ($leafZ - $z) * ($leafZ - $z);

                    if ($distSq <= $leafRadiusSq || ($leafRadius > 0 && $distSq <= ($leafRadius + 1) * ($leafRadius + 1))) {
                        $chunk->setBlockRuntimeID($leafX, $leafY, $leafZ, 0, $leavesBlock);
                    }
                }
            }
        }
    }
}

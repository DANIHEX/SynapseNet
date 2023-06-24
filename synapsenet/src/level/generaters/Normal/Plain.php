<?php

namespace synapsenet\level\generators\Normal;

use synapsenet\level\Biome;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;
use synapsenet\level\Perlin;

class Plain {
    public static function generator($x, $y, $z, $chunkX, $chunkZ, $chunk, $blockMap) {
        $bedrock = BlockFactory::createBlock(BlockIds::BEDROCK);
        $stone = BlockFactory::createBlock(BlockIds::STONE);
        $grass = BlockFactory::createBlock(BlockIds::GRASS);
        $dirt = BlockFactory::createBlock(BlockIds::DIRT);
        $oakLog = BlockFactory::createBlock(BlockIds::OAK_LOG);
        $oakLeaves = BlockFactory::createBlock(BlockIds::OAK_LEAVES);

        $chunk->getBiome($y >> 4)->setBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, Biome::PLAIN);

        if ($y === 0) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $bedrock);
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

            if (mt_rand(1, 50) === 1) {
                // Generate ores
                self::generateOres($x, $y, $z, $chunk);
            }
        }
    }

    private static function generateTree($x, $y, $z, $chunk, $logBlock, $leavesBlock) {
        $treeHeight = mt_rand(4, 7);
        $leafStart = $y + $treeHeight - 3;

        // Generate trunk
        for ($i = $y; $i < $y + $treeHeight; $i++) {
            $chunk->setBlockRuntimeID($x, $i, $z, 0, $logBlock);
        }

        // Generate leaves
        for ($leafY = $leafStart; $leafY <= $y + $treeHeight; $leafY++) {
            $leafRadius = $leafY - ($y + $treeHeight);
            $leafRadiusSq = $leafRadius * $leafRadius;

            for ($leafX = $x - 2; $leafX <= $x + 2; $leafX++) {
                for ($leafZ = $z - 2; $leafZ <= $z + 2; $leafZ++) {
                    $distSq = ($leafX - $x) * ($leafX - $x) + ($leafZ - $z) * ($leafZ - $z);

                    if ($distSq <= $leafRadiusSq || ($leafRadius > 0 && $distSq <= ($leafRadius + 1) * ($leafRadius + 1))) {
                        $chunk->setBlockRuntimeID($leafX, $leafY, $leafZ, 0, $leavesBlock);
                    }
                }
            }
        }
    }
    
    private static function generateOres($x, $y, $z, $chunk) {
        $oreBlocks = [
            BlockIds::COAL_ORE,
            BlockIds::IRON_ORE,
            BlockIds::GOLD_ORE,
            BlockIds::REDSTONE_ORE,
            BlockIds::LAPIS_LAZULI_ORE,
            BlockIds::DIAMOND_ORE,
            BlockIds::EMERALD_ORE
        ];

        $oreCount = mt_rand(1, 5); // Randomize the number of ore blocks to generate

        for ($i = 0; $i < $oreCount; $i++) {
            $oreBlockId = $oreBlocks[array_rand($oreBlocks)];
            $oreBlock = BlockFactory::createBlock($oreBlockId);
            $oreX = $x + mt_rand(0, 15);
            $oreY = mt_rand(5, 63);
            $oreZ = $z + mt_rand(0, 15);

            $chunk->setBlockRuntimeID($oreX, $oreY, $oreZ, 0, $oreBlock);
        }
    }
}

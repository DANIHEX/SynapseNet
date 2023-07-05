<?php

namespace synapsenet\level\generators\Normal;

use synapsenet\level\Biome;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;

class Taiga {
    public static function generator($x, $y, $z, $chunkX, $chunkZ, Chunk $chunk, $blockMap) {
        $stone = BlockFactory::createBlock(BlockIds::STONE);
        $dirt = BlockFactory::createBlock(BlockIds::DIRT);
        $grass = BlockFactory::createBlock(BlockIds::GRASS);
        $spruceLog = BlockFactory::createBlock(BlockIds::SPRUCE_LOG);
        $spruceLeaves = BlockFactory::createBlock(BlockIds::SPRUCE_LEAVES);
        $podzol = BlockFactory::createBlock(BlockIds::PODZOL);
        $snow = BlockFactory::createBlock(BlockIds::SNOW_LAYER);

        if (Biome::getBiomeId($chunk->getBiomeId($x & 0x0f, $z & 0x0f)) === Biome::TAIGA) {
            if ($y === 0) {
                $chunk->setBlock($x, $y, $z, $stone);
            } elseif ($y < 4) {
                $chunk->setBlock($x, $y, $z, $stone);
            } elseif ($y < 128) {
                $chunk->setBlock($x, $y, $z, $stone);

                if ($y === 4) {
                    // Generate grass or podzol
                    $noise = Perlin::octaveNoise2D($x + ($chunkX << 4), $z + ($chunkZ << 4), 0.0625, 2) / 8.0;

                    if ($noise + ($y >> 5) > 0.3) {
                        $chunk->setBlock($x, $y, $z, $grass);
                    } else {
                        $chunk->setBlock($x, $y, $z, $podzol);
                    }
                }

                if ($y === 5) {
                    // Generate ferns
                    if (mt_rand(1, 10) === 1) {
                        $chunk->setBlock($x, $y, $z, BlockFactory::get(BlockIds::FERN));
                    }
                }

                if ($y > 5 && $y < 128) {
                    // Generate spruce trees
                    $noise = Perlin::octaveNoise2D($x + ($chunkX << 4), $z + ($chunkZ << 4), 0.0625, 2);

                    if ($noise > 0.1) {
                        self::generateSpruceTree($x, $y, $z, $chunk, $spruceLog, $spruceLeaves);
                    }
                }

                if ($y > 100) {
                    // Generate snow
                    $chunk->setBlock($x, $y, $z, $snow);
                }
            }
        }
    }

    private static function generateSpruceTree($x, $y, $z, $chunk, $logBlock, $leavesBlock) {
        $treeHeight = mt_rand(6, 8);
    
        // Generate trunk
        for ($i = 0; $i < $treeHeight; $i++) {
            $chunk->setBlock($x, $y + $i, $z, $logBlock);
        }
    
        // Generate branches
        for ($i = 0; $i < $treeHeight; $i++) {
            $branchLength = $treeHeight - $i;
            $branchRadius = ($branchLength > 1) ? 1 : 0;
    
            for ($branchX = -2; $branchX <= 2; $branchX++) {
                for ($branchZ = -2; $branchZ <= 2; $branchZ++) {
                    if (abs($branchX) !== 2 || abs($branchZ) !== 2) {
                        $chunk->setBlock($x + $branchX, $y + $i, $z + $branchZ, $leavesBlock);
                    }
                }
            }
        }
    
        // Generate top
        $topRadius = 3;
        $topHeight = 2;
        for ($topY = $y + $treeHeight; $topY < $y + $treeHeight + $topHeight; $topY++) {
            for ($topX = -$topRadius; $topX <= $topRadius; $topX++) {
                for ($topZ = -$topRadius; $topZ <= $topRadius; $topZ++) {
                    $distSq = $topX * $topX + $topZ * $topZ;
                    if ($distSq <= $topRadius * $topRadius) {
                        $chunk->setBlock($x + $topX, $topY, $z + $topZ, $leavesBlock);
                    }
                }
            }
        }
    }    
}

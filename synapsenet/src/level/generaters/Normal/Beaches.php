<?php

namespace synapsenet\level\generators\Normal;

use synapsenet\level\Biome;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;
use synapsenet\level\Perlin;
use synapsenet\level\structures\AbandonedShipwreck;

class Beaches {
    public static function generator($x, $y, $z, $chunkX, $chunkZ, $chunk, $blockMap) {
        $sand = BlockFactory::createBlock(BlockIds::SAND);
        $sugarCane = BlockFactory::createBlock(BlockIds::SUGARCANE_BLOCK);
        $water = BlockFactory::createBlock(BlockIds::WATER);

        $chunk->getBiome($y >> 4)->setBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, Biome::BEACH);

        if ($y === 0) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, BlockFactory::createBlock(BlockIds::BEDROCK));
        } elseif ($y < 62) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $water);
        } elseif ($y === 62) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, BlockFactory::createBlock(BlockIds::GRAVEL));
        } else {
            $perlin = new Perlin();
            $noise = $perlin->noise(($chunkX << 4) + $x + 0.5, $y + 0.5, ($chunkZ << 4) + $z + 0.5, 8, 3);

            if ($noise < -0.25) {
                $chunk->setBlockRuntimeID($x, $y, $z, 0, $sand);

                if ($noise < -0.6 && $chunk->getBlockRuntimeID($x, $y + 1, $z) === 0) {
                    $chunk->setBlockRuntimeID($x, $y + 1, $z, 0, $sugarCane);
                }
            } else {
                $chunk->setBlockRuntimeID($x, $y, $z, 0, BlockFactory::createBlock(BlockIds::DIRT));
            }

            if (mt_rand(1, 10) === 1) {
                // Generate abandoned shipwreck
                AbandonedShipwreck::generate($x - 2, $y, $z - 3, $chunk);
            }
        }
    }
}

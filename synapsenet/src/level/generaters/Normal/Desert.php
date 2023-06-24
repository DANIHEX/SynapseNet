<?php

namespace synapsenet\level\generators\Normal;

use synapsenet\level\Biome;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;
use synapsenet\level\Perlin;

class Desert {
    public static function generator($x, $y, $z, $chunkX, $chunkZ, $chunk, $blockMap) {
        $bedrock = BlockFactory::createBlock(BlockIds::BEDROCK);
        $sand = BlockFactory::createBlock(BlockIds::SAND);
        $stone = BlockFactory::createBlock(BlockIds::STONE);
        $cactus = BlockFactory::createBlock(BlockIds::CACTUS);
        $air = BlockFactory::get(BlockIds::AIR);

        $chunk->getBiome($y >> 4)->setBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, Biome::DESERT);

        if ($y === 0) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $bedrock);
        } elseif ($y < 4) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $stone);
        } elseif ($y < 128) {
            $perlin = new Perlin();
            $noise = $perlin->noise(($chunkX << 4) + $x + 0.5, $y + 0.5, ($chunkZ << 4) + $z + 0.5, 8, 3);

            if ($y < 64) {
                $chunk->setBlockRuntimeID($x, $y, $z, 0, $sand);
            } else {
                $chunk->setBlockRuntimeID($x, $y, $z, 0, $air);
            }

            if ($y === 63 && $noise >= 0) {
                // Generate cactus
                if (mt_rand(1, 10) === 1) {
                    self::generateCactus($x, $y + 1, $z, $chunk, $cactus);
                }
            }
        }
    }

    private static function generateCactus($x, $y, $z, $chunk, $cactusBlock) {
        $cactusHeight = mt_rand(3, 4);

        for ($i = $y; $i < $y + $cactusHeight; $i++) {
            $chunk->setBlockRuntimeID($x, $i, $z, 0, $cactusBlock);
        }
    }
}

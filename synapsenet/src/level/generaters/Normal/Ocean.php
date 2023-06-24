<?php

namespace synapsenet\level\generators\Normal;

use synapsenet\level\Perlin;
use synapsenet\level\Biome;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;

class Ocean {
    public function generator($x, $y, $z, $chunkX, $chunkZ, $chunk, $blockMap) {
        $bedrock = BlockFactory::createBlock(BlockIds::BEDROCK);
        $stone = BlockFactory::createBlock(BlockIds::STONE);
        $water = BlockFactory::createBlock(BlockIds::WATER);
        $perlin = new Perlin();

        $chunk->getBiome($y >> 4)->setBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, Biome::OCEAN);

        for ($bedrockY = 0; $bedrockY < 128; ++$bedrockY) {
            if ($bedrockY === 0) {
                $chunk->setBlockRuntimeID($x, $bedrockY, $z, 0, $bedrock);
            }

            $noise = $perlin->noise(($chunkX << 4) + $x + 0.5, $y + 0.5, ($chunkZ << 4) + $z + 0.5, 8, 3);

            if ($noise > 0) {
                $chunk->setBlockRuntimeID($x, $y, $z, 0, $stone);
            } elseif ($y <= 62) {
                $chunk->setBlockRuntimeID($x, $y, $z, 0, $water);
            }
        }

        if (mt_rand(1, 50) === 1) {
            // Generate ores
            self::generateOres($x, $y, $z, $chunk);
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
            $oreY = mt_rand(5, 61);
            $oreZ = $z + mt_rand(0, 15);

            $chunk->setBlockRuntimeID($oreX, $oreY, $oreZ, 0, $oreBlock);
        }
    }
}

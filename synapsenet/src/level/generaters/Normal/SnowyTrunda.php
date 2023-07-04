<?php

namespace synapsenet\level\generators\Normal;

// Class should be improved in the future.

use synapsenet\level\Biome;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;

class SnowyTundra {
    public static function generator($x, $y, $z, $chunkX, $chunkZ, $chunk, $blockMap) {
        $bedrock = BlockFactory::createBlock(BlockIds::BEDROCK);
        $stone = BlockFactory::createBlock(BlockIds::STONE);
        $snowBlock = BlockFactory::createBlock(BlockIds::SNOW_BLOCK);

        $chunk->getBiome($y >> 4)->setBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, Biome::SNOWY_TUNDRA);

        if ($y === 0) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $bedrock);
        } elseif ($y < 4) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $stone);
        } elseif ($y < 128) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $snowBlock);
        }
    }
}

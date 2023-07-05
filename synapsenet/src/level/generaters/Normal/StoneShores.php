<?php

namespace synapsenet\level\generators\Normal;

use synapsenet\level\Biome;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;

class StoneShores {
    public function generator($x, $y, $z, $chunkX, $chunkZ, $chunk, $blockMap) {
        $stone = BlockFactory::createBlock(BlockIds::STONE);
        $gravel = BlockFactory::createBlock(BlockIds::GRAVEL);
        $water = BlockFactory::createBlock(BlockIds::WATER);

        // Set the biome of the chunk to StoneShores
        $chunk->getBiome($y >> 4)->setBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, Biome::STONE_SHORES);

        // Generate the terrain based on the y-coordinate
        if ($y === 0) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $stone); // Bottom layer is stone
        } elseif ($y < 4) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $gravel); // Layers 1-3 are gravel
        } elseif ($y < 62) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $stone); // Layers 4-61 are stone
        } elseif ($y === 62) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $gravel); // Layer 62 is gravel
        } else {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $water); // Layers above 62 are water
        }
    }
}

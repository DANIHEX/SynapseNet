<?php

namespace synapsenet\level\generators\Normal;

use synapsenet\level\Biome;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;
use synapsenet\level\structures\OakTree;
use synapsenet\level\structures\BirchTree;
use synapsenet\level\structures\DarkOakTree;

class Forest {
    public static function generator($x, $y, $z, $chunkX, $chunkZ, $chunk, $blockMap) {
        $bedrock = BlockFactory::createBlock(BlockIds::BEDROCK);
        $stone = BlockFactory::createBlock(BlockIds::STONE);
        $grass = BlockFactory::createBlock(BlockIds::GRASS);
        $dirt = BlockFactory::createBlock(BlockIds::DIRT);

        $chunk->getBiome($y >> 4)->setBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, Biome::FOREST);

        if ($y === 0) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $bedrock);
        } elseif ($y < 4) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $stone);
        } elseif ($y < 128) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $grass);

            if ($y === 4) {
                // Generate trees
                $random = mt_rand(1, 10);
                if ($random === 1) {
                    OakTree::generate($x, $y + 1, $z, $chunk);
                } elseif ($random === 2) {
                    BirchTree::generate($x, $y + 1, $z, $chunk);
                } elseif ($random === 3) {
                    DarkOakTree::generate($x, $y + 1, $z, $chunk);
                }
            }
        }
    }
}

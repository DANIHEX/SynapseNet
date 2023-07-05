<?php

namespace synapsenet\level\biomes;

use synapsenet\level\structures\AcaciaTree;
use synapsenet\level\structures\Village;
use synapsenet\level\structures\PillagerOutpost;
use synapsenet\level\Chunk;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;

class Savannah extends Biome {
    public function __construct() {
        parent::__construct(self::SAVANNAH);
    }

    public function generator($chunkX, $chunkZ, Chunk $chunk, $blockMap) {
        $grass = BlockFactory::get(BlockIds::GRASS);
        $dirt = BlockFactory::get(BlockIds::DIRT);
        $stone = BlockFactory::get(BlockIds::STONE);

        // Set biome-specific blocks
        $chunk->setBiomeIdArray(array_fill(0, 256, $this->biomeId));
        $chunk->setBlockIdArray($blockMap);
        $chunk->setBlockDataArray(array_fill(0, 65536, 0));

        // Generate terrain
        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                $chunk->setBlockId($x, 0, $z, $stone->getId());
                $chunk->setBlockId($x, 1, $z, $dirt->getId());
                $chunk->setBlockId($x, 2, $z, $dirt->getId());

                // Generate grass on top layer
                $chunk->setBlockId($x, 63, $z, $grass->getId());
            }
        }
    }

    public function generateStructures($chunkX, $chunkZ, Chunk $chunk) {
        // Generate Acacia Trees
        for ($i = 0; $i < 5; ++$i) {
            $x = ($chunkX << 4) + mt_rand(0, 15);
            $z = ($chunkZ << 4) + mt_rand(0, 15);
            $y = $chunk->getHighestBlockAt($x & 0x0f, $z & 0x0f) - 1;

            AcaciaTree::generate($x, $y, $z, $chunk);
        }

        // Generate Villages
        // if (mt_rand(0, 10) === 0) {
        //     $x = ($chunkX << 4) + mt_rand(0, 15);
        //     $z = ($chunkZ << 4) + mt_rand(0, 15);
        //     $y = $chunk->getHighestBlockAt($x & 0x0f, $z & 0x0f) - 1;

        //     Village::generate($x, $y, $z, $chunk);
        // }

        // Generate Pillager Outposts
        if (mt_rand(0, 10) === 0) {
            $x = ($chunkX << 4) + mt_rand(0, 15);
            $z = ($chunkZ << 4) + mt_rand(0, 15);
            $y = $chunk->getHighestBlockAt($x & 0x0f, $z & 0x0f) - 1;

            PillagerOutpost::generate($x, $y, $z, $chunk);
        }
    }
}

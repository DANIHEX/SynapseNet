<?php

namespace synapsenet\level\generator;

use synapsenet\level\Generator;
use synapsenet\level\chunk\Chunk;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;

class Flat extends Generator {
    public static $generatorName = "flat";

    public function generate($x, $z) {
        $chunk = new Chunk($x, $z, BlockFactory::createBlock(BlockIds::AIR));

        $bedrock = BlockFactory::createBlock(BlockIds::STONE);
        $dirt = BlockFactory::createBlock(BlockIds::DIRT);
        $grass = BlockFactory::createBlock(BlockIds::GRASS);

        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                $chunk->setBlockRuntimeID($x, 0, $z, $bedrock);
                $chunk->setBlockRuntimeID($x, 1, $z, $dirt);
                $chunk->setBlockRuntimeID($x, 2, $z, $dirt);
                $chunk->setBlockRuntimeID($x, 3, $z, $grass);
            }
        }

        return $chunk;
    }
}

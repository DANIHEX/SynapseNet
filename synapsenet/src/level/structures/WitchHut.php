<?php

namespace synapsenet\level\structures;

use synapsenet\level\Chunk;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;

class WitchHut {
    public static function generate($x, $y, $z, $chunk) {
        // needs to be improved :skull:
        $oakWood = BlockFactory::get(BlockIds::OAK_WOOD);
        $oakPlanks = BlockFactory::get(BlockIds::OAK_PLANKS);
        $spruceStairs = BlockFactory::get(BlockIds::SPRUCE_STAIRS);
        $air = BlockFactory::get(BlockIds::AIR);

        // Generate hut walls and floor
        for ($i = 0; $i < 5; ++$i) {
            for ($j = 0; $j < 4; ++$j) {
                $chunk->setBlock($x + $i, $y, $z + $j, $oakPlanks);
            }
        }

        // Generate hut roof
        $chunk->setBlock($x + 1, $y + 1, $z, $spruceStairs->setMeta(3));
        $chunk->setBlock($x + 2, $y + 1, $z, $spruceStairs->setMeta(3));
        $chunk->setBlock($x + 3, $y + 1, $z, $spruceStairs->setMeta(3));
        $chunk->setBlock($x, $y + 1, $z + 1, $spruceStairs->setMeta(1));
        $chunk->setBlock($x, $y + 1, $z + 2, $spruceStairs->setMeta(1));
        $chunk->setBlock($x, $y + 1, $z + 3, $spruceStairs->setMeta(1));
        $chunk->setBlock($x + 4, $y + 1, $z + 1, $spruceStairs->setMeta(0));
        $chunk->setBlock($x + 4, $y + 1, $z + 2, $spruceStairs->setMeta(0));
        $chunk->setBlock($x + 4, $y + 1, $z + 3, $spruceStairs->setMeta(0));
        $chunk->setBlock($x + 1, $y + 1, $z + 4, $spruceStairs->setMeta(2));
        $chunk->setBlock($x + 2, $y + 1, $z + 4, $spruceStairs->setMeta(2));
        $chunk->setBlock($x + 3, $y + 1, $z + 4, $spruceStairs->setMeta(2));

        // Generate hut entrance
        $chunk->setBlock($x + 2, $y + 1, $z - 1, $air);
        $chunk->setBlock($x + 2, $y + 2, $z - 1, $air);
    }
}

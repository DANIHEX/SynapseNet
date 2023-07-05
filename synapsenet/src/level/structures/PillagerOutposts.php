<?php

namespace synapsenet\level\structures;

// someone help im confused - INSTRUCTIONS UNCLEARED - Please try again

use synapsenet\level\Chunk;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;

class PillagerOutpost {
    public static function generate($x, $y, $z, $chunk) {
        $log = BlockFactory::get(BlockIds::OAK_LOG);
        $planks = BlockFactory::get(BlockIds::OAK_PLANKS);
        $stairs = BlockFactory::get(BlockIds::OAK_STAIRS);
        $slab = BlockFactory::get(BlockIds::OAK_SLAB);
        $fence = BlockFactory::get(BlockIds::OAK_FENCE);
        $torch = BlockFactory::get(BlockIds::TORCH);
        $air = BlockFactory::get(BlockIds::AIR);

        // Generate outpost structure
        $chunk->setBlock($x, $y, $z, $log); // Main support
        $chunk->setBlock($x, $y + 1, $z, $planks); // Platform

        // Generate walls
        for ($yo = 2; $yo < 5; ++$yo) {
            $chunk->setBlock($x, $y + $yo, $z, $log); // Vertical logs
        }

        for ($xo = -2; $xo < 3; ++$xo) {
            for ($yo = 2; $yo < 4; ++$yo) {
                for ($zo = -2; $zo < 3; ++$zo) {
                    if (abs($xo) === 2 || abs($zo) === 2) {
                        $chunk->setBlock($x + $xo, $y + $yo, $z + $zo, $planks); // Wall panels
                    }
                }
            }
        }

        // Generate roof
        for ($xo = -3; $xo < 4; ++$xo) {
            for ($zo = -3; $zo < 4; ++$zo) {
                if (abs($xo) === 3 || abs($zo) === 3) {
                    $chunk->setBlock($x + $xo, $y + 4, $z + $zo, $planks); // Roof panels
                }
            }
        }

        // Generate stairs for entrance
        $chunk->setBlock($x + 1, $y + 2, $z, $stairs, 0); // Stairs facing east
        $chunk->setBlock($x - 1, $y + 2, $z, $stairs, 1); // Stairs facing west
        $chunk->setBlock($x, $y + 2, $z + 1, $stairs, 2); // Stairs facing south
        $chunk->setBlock($x, $y + 2, $z - 1, $stairs, 3); // Stairs facing north

        // Generate fence around the outpost
        for ($xo = -3; $xo < 4; ++$xo) {
            for ($yo = 5; $yo < 7; ++$yo) {
                for ($zo = -3; $zo < 4; ++$zo) {
                    if (abs($xo) === 3 || abs($zo) === 3) {
                        $chunk->setBlock($x + $xo, $y + $yo, $z + $zo, $fence);
                    }
                }
            }
        }

        // Generate torches on top of the walls
        $chunk->setBlock($x + 2, $y + 5, $z, $torch); // Torch facing east
        $chunk->setBlock($x - 2, $y + 5, $z, $torch); // Torch facing west
        $chunk->setBlock($x, $y + 5, $z + 2, $torch); // Torch facing south
        $chunk->setBlock($x, $y + 5, $z - 2, $torch); // Torch facing north

        // Clear area inside the outpost
        for ($xo = -2; $xo < 3; ++$xo) {
            for ($yo = 2; $yo < 5; ++$yo) {
                for ($zo = -2; $zo < 3; ++$zo) {
                    $chunk->setBlock($x + $xo, $y + $yo, $z + $zo, $air);
                }
            }
        }
    }
}

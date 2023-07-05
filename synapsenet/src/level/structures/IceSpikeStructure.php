<?php

namespace synapsenet\level\structures;

use synapsenet\level\Chunk;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;
use pocketmine\utils\Random;

class IceSpikeStructure {
    public static function generate(Chunk $chunk, Random $random, $x, $y, $z) {
        $packedIce = BlockFactory::createBlock(BlockIds::PACKED_ICE);
        $ice = BlockFactory::createBlock(BlockIds::ICE);

        // Generate the central ice spike
        $height = $random->nextBoundedInt(3) + 4;

        for ($i = 0; $i < $height; $i++) {
            $chunk->setBlock($x, $y + $i, $z, $packedIce);
        }

        // Generate smaller ice spikes around the central spike
        $spikeCount = $random->nextBoundedInt(3) + 2;

        for ($i = 0; $i < $spikeCount; $i++) {
            $spikeHeight = $random->nextBoundedInt(2) + 2;

            for ($j = 0; $j < $spikeHeight; $j++) {
                $chunk->setBlock($x - 1, $y + $j, $z + 1, $ice);
                $chunk->setBlock($x + 1, $y + $j, $z + 1, $ice);
                $chunk->setBlock($x - 1, $y + $j, $z - 1, $ice);
                $chunk->setBlock($x + 1, $y + $j, $z - 1, $ice);
                $chunk->setBlock($x, $y + $j, $z, $packedIce);
            }

            $x += $random->nextBoundedInt(3) - 1;
            $z += $random->nextBoundedInt(3) - 1;
        }
    }
}

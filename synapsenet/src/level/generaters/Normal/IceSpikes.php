<?php

namespace synapsenet\level\generators\Normal;

use synapsenet\level\Biome;
use synapsenet\level\structures\IceSpikeStructure;
use synapsenet\level\Chunk;
use pocketmine\utils\Random;

class IceSpikes {
    public static function generator($x, $y, $z, $chunkX, $chunkZ, Chunk $chunk, $blockMap, Random $random) {
        $packedIce = BlockFactory::createBlock(BlockIds::PACKED_ICE);

        $chunk->getBiome($y >> 4)->setBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, Biome::ICE_SPIKES);

        if ($y <= 120 && $random->nextFloat() < 0.08) {
            $structureX = ($chunkX << 4) + $x;
            $structureY = $y + 1;
            $structureZ = ($chunkZ << 4) + $z;

            IceSpikeStructure::generate($chunk, $random, $structureX, $structureY, $structureZ);
        } else {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $packedIce);
        }
    }
}

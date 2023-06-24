<?php

namespace synapsenet\level\generators;

use synapsenet\level\chunk\Chunk;
use synapsenet\level\Generator;
use synapsenet\level\Perlin;
use synapsenet\level\generators\Normal\{Ocean, Plain};

class Overworld extends Generator {
    public static $generatorName = "overworld";

    public function generate($chunkX, $chunkZ) {
        $seed = 1;
        $air = BlockFactory::createBlock(BlockIds::AIR);
        $chunk = new Chunk($chunkX, $chunkZ, $air);
        $perlin = new Perlin();
        $ocean = new Ocean();
        $plain = new Plain();

        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                for ($y = 0; $y < 256; ++$y) {
                    if ($y < 62) {
                        $ocean->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    } else {
                        $plain->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    }
                }
            }
        }

        return $chunk;
    }
}

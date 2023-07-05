<?php

namespace synapsenet\level\generators;

// needs to be heavenly improved

use synapsenet\level\chunk\Chunk;
use synapsenet\level\Generator;
use synapsenet\level\Perlin;
use synapsenet\level\generators\Normal\{Ocean, Plain, Beaches, SnowyTundra, IceSpikes, Forest, Taiga, Mountains, Swamps, Savannas, StoneShores, Jungle};
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;

class Overworld extends Generator {
    public static $generatorName = "overworld";

    public function generate($chunkX, $chunkZ) {
        $seed = 1; // seed must be random for know this.
        $air = BlockFactory::createBlock(BlockIds::AIR);
        $chunk = new Chunk($chunkX, $chunkZ, $air);
        $perlin = new Perlin();
        $ocean = new Ocean();
        $plain = new Plain();
        $beaches = new Beaches();
        $snowyTundra = new SnowyTundra();
        $iceSpikes = new IceSpikes();
        $forest = new Forest();
        $taiga = new Taiga();
        $mountains = new Mountains();
        $swamps = new Swamps();
        $savannas = new Savannas();
        $stoneShores = new StoneShores();
        $jungle = new Jungle();

        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                for ($y = 0; $y < 256; ++$y) {
                    if ($y < 62) {
                        $ocean->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    } elseif ($y < 63) {
                        $beaches->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    } elseif ($y < 90) {
                        $snowyTundra->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    } elseif ($y < 110) {
                        $iceSpikes->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    } elseif ($y < 130) {
                        $forest->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    } elseif ($y < 150) {
                        $taiga->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    } elseif ($y < 170) {
                        $mountains->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    } elseif ($y < 190) {
                        $swamps->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    } elseif ($y < 210) {
                        $savannas->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    } elseif ($y < 230) {
                        $stoneShores->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    } else {
                        $jungle->generator($x, $y, $z, $chunkX, $chunkZ, $chunk);
                    }
                }
            }
        }

        return $chunk;
    }
}

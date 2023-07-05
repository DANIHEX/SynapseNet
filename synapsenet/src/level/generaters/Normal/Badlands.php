<?php

namespace synapsenet\level\generators\Normal;

use synapsenet\level\Biome;
use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;
use synapsenet\level\Perlin;

class Badlands {
    public static function generator($x, $y, $z, $chunkX, $chunkZ, $chunk, $blockMap) {
        $bedrock = BlockFactory::createBlock(BlockIds::BEDROCK);
        $stone = BlockFactory::createBlock(BlockIds::STONE);
        $redSand = BlockFactory::createBlock(BlockIds::RED_SAND);
        $goldOre = BlockFactory::createBlock(BlockIds::GOLD_ORE);
        $oakLog = BlockFactory::createBlock(BlockIds::OAK_LOG);
        $oakLeaves = BlockFactory::createBlock(BlockIds::OAK_LEAVES);
        
        $chunk->getBiome($y >> 4)->setBlockRuntimeID($x & 0x0f, $y & 0x0f, $z & 0x0f, Biome::BADLANDS);

        if ($y === 0) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $bedrock);
        } elseif ($y < 4) {
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $stone);
        } elseif ($y < 128) {
            // Generate red sand and gold ore
            $chunk->setBlockRuntimeID($x, $y, $z, 0, $redSand);
            
            // Generate gold ore veins
            self::generateGoldOre($x, $y, $z, $chunk, $goldOre);
            
            if ($y === 4) {
                // Generate oak trees
                self::generateTrees($x, $y + 1, $z, $chunk, $oakLog, $oakLeaves);
            }
        }
    }
    
    private static function generateGoldOre($x, $y, $z, $chunk, $goldOreBlock) {
        $veinCount = mt_rand(1, 4); // Randomize the number of gold ore veins
        
        for ($i = 0; $i < $veinCount; $i++) {
            $veinSize = mt_rand(3, 8); // Randomize the size of each gold ore vein
            $veinX = $x + mt_rand(0, 15);
            $veinY = mt_rand(5, 61);
            $veinZ = $z + mt_rand(0, 15);
            
            for ($j = 0; $j < $veinSize; $j++) {
                $chunk->setBlockRuntimeID($veinX, $veinY, $veinZ, 0, $goldOreBlock);
                
                // Randomize the next block position within a small range
                $veinX += mt_rand(-1, 1);
                $veinY += mt_rand(-1, 1);
                $veinZ += mt_rand(-1, 1);
            }
        }
    }
    
    private static function generateTrees($x, $y, $z, $chunk, $logBlock, $leavesBlock) {
        $treeCount = mt_rand(1, 3); // Randomize the number of oak trees
        
        for ($i = 0; $i < $treeCount; $i++) {
            $treeX = $x + mt_rand(0, 15);
            $treeZ = $z + mt_rand(0, 15);
            
            // Generate oak tree trunk
            for ($j = 0; $j < 4; $j++) {
                $chunk->setBlockRuntimeID($treeX, $y + $j, $treeZ, 0, $logBlock);
            }
            
            // Generate oak tree leaves
            for ($leafY = $y + 3; $leafY <= $y + 5; $leafY++) {
                for ($leafX = $treeX - 2; $leafX <= $treeX + 2; $leafX++) {
                    for ($leafZ = $treeZ - 2; $leafZ <= $treeZ + 2; $leafZ++) {
                        $chunk->setBlockRuntimeID($leafX, $leafY, $leafZ, 0, $leavesBlock);
                    }
                }
            }
        }
    }
}

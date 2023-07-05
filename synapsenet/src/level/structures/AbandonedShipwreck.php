<?php

namespace synapsenet\level\structures;

use synapsenet\block\BlockFactory;
use synapsenet\block\BlockIds;
use synapsenet\level\Chunk;
use synapsenet\item\{ItemFactory, ItemIds};

class AbandonedShipwreck {
    private const WIDTH = 5;
    private const LENGTH = 7;
    private const HEIGHT = 4;

    public static function generate($x, $y, $z, Chunk $chunk) {
        $planks = BlockFactory::createBlock(BlockIds::PLANKS);
        $slab = BlockFactory::createBlock(BlockIds::WOODEN_SLAB);
        $trapdoor = BlockFactory::createBlock(BlockIds::TRAPDOOR);
        $chest = BlockFactory::createBlock(BlockIds::CHEST);

        $chestX = $x + 1;
        $chestY = $y + 1;
        $chestZ = $z + 2;

        // Generate shipwreck blocks
        for ($i = 0; $i < self::WIDTH; ++$i) {
            for ($j = 0; $j < self::LENGTH; ++$j) {
                for ($k = 0; $k < self::HEIGHT; ++$k) {
                    if ($k === 0 || $k === self::HEIGHT - 1) {
                        $block = $planks;
                    } else {
                        $block = $slab;
                        $block->setDamage(2); // Use the upper half of the slab block
                    }

                    $chunk->setBlock($x + $i, $y + $k, $z + $j, $block);
                }
            }
        }

        // Generate trapdoor
        $chunk->setBlock($x + 2, $y + self::HEIGHT - 1, $z + 3, $trapdoor, 1); // Face south

        // Generate chest
        $chunk->setBlock($chestX, $chestY, $chestZ, $chest);

        // Place random items in the chest
        $chestTile = $chunk->getTile($chestX, $chestY, $chestZ);
        if ($chestTile !== null) {
            $items = self::generateChestItems();
            foreach ($items as $item) {
                $chestTile->getInventory()->addItem($item);
            }
        }
    }

    private static function generateChestItems() {
        // Define the possible items to be placed in the chest
        $possibleItems = [
            ItemFactory::createItem(ItemIds::DIAMOND, 0, mt_rand(1, 3)),
            ItemFactory::createItem(ItemIds::GOLD_INGOT, 0, mt_rand(2, 5)),
            ItemFactory::createItem(ItemIds::IRON_INGOT, 0, mt_rand(3, 7)),
            ItemFactory::createItem(ItemIds::COAL, 0, mt_rand(5, 10)),
            ItemFactory::createItem(ItemIds::BREAD, 0, mt_rand(3, 7)),
            ItemFactory::createItem(ItemIds::APPLE, 0, mt_rand(2, 5)),
            ItemFactory::createItem(ItemIds::ENDER_PEARL, 0, mt_rand(1, 3)),
        ];

        // Randomly select items from the possible items
        $chestItems = [];
        $numItems = mt_rand(3, 7);
        for ($i = 0; $i < $numItems; $i++) {
            $randomItem = $possibleItems[array_rand($possibleItems)];
            $chestItems[] = $randomItem;
        }

        return $chestItems;
    }
}

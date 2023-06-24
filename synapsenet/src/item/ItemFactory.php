<?php

namespace synapsenet\item;

class ItemFactory {
    /**
     * @param int $itemId
     * @param int $count
     *
     * @return Item|null
     */
    public static function createItem(int $itemId, int $count = 1): ?Item {
        return match ($itemId) {
            ItemIds::AIR => new Item(ItemIds::AIR, "Air", $count),
            ItemIds::STONE => new Item(ItemIds::STONE, "Stone", $count),
            ItemIds::GRANITE => new Item(ItemIds::GRANITE, "Granite", $count),
            ItemIds::POLISHED_GRANITE => new Item(ItemIds::POLISHED_GRANITE, "Polished Granite", $count),
            ItemIds::DIORITE => new Item(ItemIds::DIORITE, "Diorite", $count),
            ItemIds::POLISHED_DIORITE => new Item(ItemIds::POLISHED_DIORITE, "Polished Diorite", $count),
            ItemIds::ANDESITE => new Item(ItemIds::ANDESITE, "Andesite", $count),
            ItemIds::POLISHED_ANDESITE => new Item(ItemIds::POLISHED_ANDESITE, "Polished Andesite", $count),
            ItemIds::GRASS_BLOCK => new Item(ItemIds::GRASS_BLOCK, "Grass Block", $count),
            ItemIds::DIRT => new Item(ItemIds::DIRT, "Dirt", $count),
            ItemIds::COBBLESTONE => new Item(ItemIds::COBBLESTONE, "Cobblestone", $count),
            ItemIds::IRON_SHOVEL => new Item(ItemIds::IRON_SHOVEL, "Iron Shovel", $count),
            ItemIds::IRON_PICKAXE => new Item(ItemIds::IRON_PICKAXE, "Iron Pickaxe", $count),
            ItemIds::IRON_AXE => new Item(ItemIds::IRON_AXE, "Iron Axe", $count),
            ItemIds::FLINT_AND_STEEL => new Item(ItemIds::FLINT_AND_STEEL, "Flint and Steel", $count),
            ItemIds::APPLE => new Item(ItemIds::APPLE, "Apple", $count),
            ItemIds::BOW => new Item(ItemIds::BOW, "Bow", $count),
            ItemIds::ARROW => new Item(ItemIds::ARROW, "Arrow", $count),
            ItemIds::COAL => new Item(ItemIds::COAL, "Coal", $count),
            ItemIds::CHARCOAL => new Item(ItemIds::CHARCOAL, "Charcoal", $count),
            ItemIds::DIAMOND => new Item(ItemIds::DIAMOND, "Diamond", $count),
            ItemIds::IRON_INGOT => new Item(ItemIds::IRON_INGOT, "Iron Ingot", $count),
            ItemIds::GOLD_INGOT => new Item(ItemIds::GOLD_INGOT, "Gold Ingot", $count),
            ItemIds::LEATHER_HELMET => new Item(ItemIds::LEATHER_HELMET, "Leather Helmet", $count),
            ItemIds::LEATHER_CHESTPLATE => new Item(ItemIds::LEATHER_CHESTPLATE, "Leather Chestplate", $count),
            ItemIds::LEATHER_LEGGINGS => new Item(ItemIds::LEATHER_LEGGINGS, "Leather Leggings", $count),
            ItemIds::LEATHER_BOOTS => new Item(ItemIds::LEATHER_BOOTS, "Leather Boots", $count),
            ItemIds::CHAINMAIL_HELMET => new Item(ItemIds::CHAINMAIL_HELMET, "Chainmail Helmet", $count),
            ItemIds::CHAINMAIL_CHESTPLATE => new Item(ItemIds::CHAINMAIL_CHESTPLATE, "Chainmail Chestplate", $count),
            ItemIds::CHAINMAIL_LEGGINGS => new Item(ItemIds::CHAINMAIL_LEGGINGS, "Chainmail Leggings", $count),
            ItemIds::CHAINMAIL_BOOTS => new Item(ItemIds::CHAINMAIL_BOOTS, "Chainmail Boots", $count),
            ItemIds::IRON_HELMET => new Item(ItemIds::IRON_HELMET, "Iron Helmet", $count),
            ItemIds::IRON_CHESTPLATE => new Item(ItemIds::IRON_CHESTPLATE, "Iron Chestplate", $count),
            ItemIds::IRON_LEGGINGS => new Item(ItemIds::IRON_LEGGINGS, "Iron Leggings", $count),
            ItemIds::IRON_BOOTS => new Item(ItemIds::IRON_BOOTS, "Iron Boots", $count),
            ItemIds::DIAMOND_HELMET => new Item(ItemIds::DIAMOND_HELMET, "Diamond Helmet", $count),
            ItemIds::DIAMOND_CHESTPLATE => new Item(ItemIds::DIAMOND_CHESTPLATE, "Diamond Chestplate", $count),
            ItemIds::DIAMOND_LEGGINGS => new Item(ItemIds::DIAMOND_LEGGINGS, "Diamond Leggings", $count),
            ItemIds::DIAMOND_BOOTS => new Item(ItemIds::DIAMOND_BOOTS, "Diamond Boots", $count),
            ItemIds::GOLDEN_HELMET => new Item(ItemIds::GOLDEN_HELMET, "Golden Helmet", $count),
            ItemIds::GOLDEN_CHESTPLATE => new Item(ItemIds::GOLDEN_CHESTPLATE, "Golden Chestplate", $count),
            ItemIds::GOLDEN_LEGGINGS => new Item(ItemIds::GOLDEN_LEGGINGS, "Golden Leggings", $count),
            ItemIds::GOLDEN_BOOTS => new Item(ItemIds::GOLDEN_BOOTS, "Golden Boots", $count),
            default => null,
        };
    }
}
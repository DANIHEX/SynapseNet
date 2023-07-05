<?php

namespace synapsenet\block;

class BlockFactory {
    private static $blocks = [];

    public static function registerBlock(Block $block) {
        self::$blocks[$block->getId()] = $block;
    }

    public static function unregisterBlock($id) {
        unset(self::$blocks[$id]);
    }

    public static function get($id) {
        if (isset(self::$blocks[$id])) {
            return self::$blocks[$id];
        }
        return null;
    }

    public static function createBlock($id, $meta = 0) {
        $block = self::get($id);
        if ($block !== null) {
            return clone $block;
        }
        return null;
    }

    public static function getAllBlocks() {
        return self::$blocks;
    }

    public static function isBlockRegistered($id) {
        return isset(self::$blocks[$id]);
    }

    public static function getRegisteredBlockIds() {
        return array_keys(self::$blocks);
    }

    public static function getRegisteredBlockNames() {
        $names = [];
        foreach (self::$blocks as $block) {
            $names[] = $block->getName();
        }
        return $names;
    }
}

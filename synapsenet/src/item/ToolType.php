<?php

namespace synapsenet\item;

class ToolType {

    public const NONE = 0;
    public const PICKAXE = 1;
    public const AXE = 2;
    public const SHOVEL = 3;
    public const HOE = 4;
    public const SWORD = 5;
    public const HAND = 6;

    /**
     * @param int $toolType
     *
     * @return string
     */
    public static function getName(int $toolType): string {
        return match ($toolType) {
            self::PICKAXE => 'Pickaxe',
            self::AXE => 'Axe',
            self::SHOVEL => 'Shovel',
            self::HOE => 'Hoe',
            self::SWORD => 'Sword',
            self::HAND => 'Hand',
            default => 'None',
        };
    }
}

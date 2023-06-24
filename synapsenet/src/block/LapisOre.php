<?php

namespace synapsenet\block;

use synapsenet\item\ItemIds;
use synapsenet\item\ToolType;
use synapsenet\item\ItemStack;

class LapisOre extends Block {

    public function __construct() {
        parent::__construct(BlockIds::LAPIS_ORE, "Lapis Lazuli Ore");
    }

    /**
     * @return float
     */
    public function getHardness(): float {
        return 3.0;
    }

    /**
     * @return bool
     */
    public function isTransparent(): bool {
        return false;
    }

    /**
     * @return bool
     */
    public function isSolid(): bool {
        return true;
    }

    /**
     * @return bool
     */
    public function canBeWalkedOn(): bool {
        return true;
    }

    /**
     * @return ItemStack[]
     */
    public function getDrops(): array {
        return [
            new ItemStack(ItemIds::LAPIS_LAZULI, 4),
        ];
    }

    /**
     * @return int
     */
    public function getToolType(): int {
        return ToolType::PICKAXE;
    }
}

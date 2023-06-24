<?php

namespace synapsenet\block;

use synapsenet\item\ItemIds;
use synapsenet\item\ToolType;
use synapsenet\item\ItemStack;

class Dirt extends Block {

    public function __construct() {
        parent::__construct(BlockIds::DIRT, "Dirt");
    }

    /**
     * @return float
     */
    public function getHardness(): float {
        return 0.5;
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
            new ItemStack(ItemIds::DIRT, 1),
        ];
    }

    /**
     * @return int
     */
    public function getToolType(): int {
        return ToolType::SHOVEL;
    }
}

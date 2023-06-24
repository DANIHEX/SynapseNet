<?php

namespace synapsenet\block;

use synapsenet\item\ToolType;

class Water extends Block {

    public function __construct() {
        parent::__construct(BlockIds::WATER, "Water");
    }

    /**
     * @return float
     */
    public function getHardness(): float {
        return -1.0; // Indicating that the block cannot be broken
    }

    /**
     * @return bool
     */
    public function isTransparent(): bool {
        return true;
    }

    /**
     * @return bool
     */
    public function isSolid(): bool {
        return false;
    }

    /**
     * @return bool
     */
    public function canBeWalkedOn(): bool {
        return false;
    }

    /**
     * @return array
     */
    public function getDrops(): array {
        return []; // No drops when breaking the Water block
    }

    /**
     * @return int
     */
    public function getToolType(): int {
        return ToolType::NONE; // No tool is required to break the Water block
    }
}

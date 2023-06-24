<?php

namespace synapsenet\block;

use synapsenet\item\ToolType;

class Leaves extends Block {

    public function __construct() {
        parent::__construct(BlockIds::LEAVES, "Leaves");
    }

    /**
     * @return float
     */
    public function getHardness(): float {
        return 0.2;
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
        return [];
    }

    /**
     * @return int
     */
    public function getToolType(): int {
        return ToolType::NONE;
    }
}

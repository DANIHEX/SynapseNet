<?php

namespace synapsenet\event\block;

use synapsenet\event\Event;
use synapsenet\world\Position;

// ??
class BlockEvent extends Event {

    /** @var Position */
    protected Position $blockPosition;

    /**
     * @param Position $blockPosition
     */
    public function __construct(Position $blockPosition) {
        $this->blockPosition = $blockPosition;
    }

    /**
     * @return Position
     */
    public function getBlockPosition(): Position {
        return $this->blockPosition;
    }

    /**
     * @param Position $blockPosition
     *
     * @return void
     */
    public function setBlockPosition(Position $blockPosition): void {
        $this->blockPosition = $blockPosition;
    }
}

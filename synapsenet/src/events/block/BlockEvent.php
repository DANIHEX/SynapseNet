<?php

namespace synapsenet\event\block;

use synapsenet\event\Event;
use synapsenet\world\Position;

class BlockEvent extends Event
{
    protected $blockPosition;
    
    public function __construct(Position $blockPosition)
    {
        $this->blockPosition = $blockPosition;
    }
    
    public function getBlockPosition(): Position
    {
        return $this->blockPosition;
    }
    
    public function setBlockPosition(Position $blockPosition): void
    {
        $this->blockPosition = $blockPosition;
    }
    
    public function getEventName(): string
    {
        return 'BlockEvent';
    }
}

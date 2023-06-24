<?php

namespace synapsenet\level\provider\anvil\types;

class RegionIndex {
    /**
     * Sector index in 4KB blocks from the start of the file
     * @var int
     */
    public $offset;
    /**
     * Sector count in 4KB blocks from the offset
     * @var int
     */
    public $length;
}
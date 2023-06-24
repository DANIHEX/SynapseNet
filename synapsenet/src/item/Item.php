<?php

namespace synapsenet\item;

class Item {

    /** @var int */
    private int $itemId;

    /** @var string */
    private string $name;

    /** @var int */
    private int $maxStackSize;

    /**
     * @param int $itemId
     * @param string $name
     * @param int $maxStackSize
     */
    public function __construct(int $itemId, string $name, int $maxStackSize = 64) {
        $this->itemId = $itemId;
        $this->name = $name;
        $this->maxStackSize = $maxStackSize;
    }

    /**
     * @return int
     */
    public function getItemId(): int {
        return $this->itemId;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getMaxStackSize(): int {
        return $this->maxStackSize;
    }
}

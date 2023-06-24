<?php

namespace synapsenet\item;

class ItemStack {

    /** @var int */
    private int $itemId;
    /** @var int */
    private int $count;
    /** @var int */
    private int $meta;

    /**
     * @param int $itemId
     * @param int $count
     * @param int $meta
     */
    public function __construct(int $itemId, int $count = 1, int $meta = 0) {
        $this->itemId = $itemId;
        $this->count = $count;
        $this->meta = $meta;
    }

    /**
     * @return int
     */
    public function getItemId(): int {
        return $this->itemId;
    }

    /**
     * @return int
     */
    public function getCount(): int {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return ItemStack
     */
    public function setCount(int $count): self {
        $this->count = $count;

        return $this;
    }

    /**
     * @return int
     */
    public function getMeta(): int {
        return $this->meta;
    }

    /**
     * @param int $meta
     *
     * @return ItemStack
     */
    public function setMeta(int $meta): self {
        $this->meta = $meta;

        return $this;
    }
}

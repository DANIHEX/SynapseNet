<?php

namespace synapsenet\event\player;

use synapsenet\item\Item;

class PlayerDropItemEvent extends PlayerEvent {

    private $item;

    public function __construct(Item $item) {
        $this->item = $item;
    }

    public function getItem(): Item {
        return $this->item;
    }
}
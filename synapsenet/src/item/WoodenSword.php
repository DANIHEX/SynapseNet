<?php

namespace synapsenet\item;

class WoodenSword extends Item {

    public function __construct() {
        parent::__construct(ItemIds::WOODEN_SWORD, "Wooden_Sword", 1);
    }
}

<?php

namespace synapsenet\item;

class IronSword extends Item {

    public function __construct() {
        parent::__construct(ItemIds::IRON_SWORD, "Iron_Sword", 1);
    }
}

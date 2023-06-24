<?php

namespace synapsenet\item;

class GoldIngot extends Item {

    public function __construct() {
        parent::__construct(ItemIds::GOLD_INGOT, "GoldIngot");
    }
}

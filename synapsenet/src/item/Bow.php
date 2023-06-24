<?php

namespace synapsenet\item;

class Bow extends Item {

    public function __construct() {
        parent::__construct(ItemIds::BOW, "Bow", 1);
    }
}

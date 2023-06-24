<?php

namespace synapsenet\item;

class Apple extends Item {

    public function __construct() {
        parent::__construct(ItemIds::APPLE, "Apple");
    }
}

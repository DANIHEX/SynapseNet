<?php

declare(strict_types = 1);

namespace synapsenet\core\thread;

class Thread extends \Thread {

    public string $name;
    public bool $onair = false;

    public function __construct(string $name){
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }

    public function shutdown(){
        $this->onair = false;
    }

}

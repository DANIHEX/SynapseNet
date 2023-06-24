<?php

declare(strict_types=1);

namespace synapsenet\core\thread;

class Thread extends \Thread {

    /** @var string */
    public string $name;

    /** @var bool */
    public bool $onair = false;

    /**
     * @param string $name
     */
    public function __construct(string $name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return void
     */
    public function shutdown(): void {
        $this->onair = false;
    }
}

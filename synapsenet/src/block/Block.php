<?php

namespace synapsenet\block;

class Block extends BlockIds {

    /** @var int */
    protected int $id;
    /** @var string */
    protected string $name;

    /**
     * @param int $id
     * @param string $name
     */
    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
}
<?php

declare(strict_types=1);

namespace synapsenet\network\protocol;

use synapsenet\binary\Buffer;

abstract class Packet extends Buffer {

    /** @var int */
    private int $id;

    /** @var string */
    public string $magic = "\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78";

    /**
     * @param int $id
     * @param string $buffer
     */
    public function __construct(int $id, string $buffer) {
        parent::__construct($buffer);

        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getPacketId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMagic(): string {
        return $this->magic;
    }

    /**
     * @param string $buffer
     *
     * @return void
     */
    public function setBuffer(string $buffer): void {
        $this->buffer = $buffer;
    }

    /**
     * @return string
     */
    public function getBuffer(): string {
        return $this->buffer;
    }

    public abstract function extract(): Packet;

    public abstract function make(): string;
}
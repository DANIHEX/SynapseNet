<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet;

use synapsenet\binary\Buffer;

abstract class RaknetPacket extends Buffer {

    /** @var int */
    private int $id;
    
    /** @var string */
    public string $magic = "\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78";

    /**
     * private int $protocol; TODO: Future use for converting packet protocol
     * 
     * @param int $id
     * @param string $buffer
     */
    public function __construct(/* int $protocol, */int $id, string $buffer) {
        parent::__construct($buffer);

        // $this->protocol = $protocol;
        $this->id = $id;
        
    }

    // public function getPacketProtocol() {
    //     return $this->protocol;
    // }

    /**
     * @return int
     */
    public function getPacketId(): int {
        return $this->id;
    }

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

    // public abstract function getDataArray(): array;

    // public abstract function extract(): Packet;

    // public abstract function make(): string;
}

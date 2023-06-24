<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets;

use synapsenet\binary\Buffer;

class Packet extends Buffer {

    // private int $protocol; TODO: Future use for converting packet protocol

    /** @var bool */
    protected bool $ready = false;

    /** @var int */
    private int $id;

    /**
     * @param int $id
     * @param string $buffer
     */
    public function __construct(/* int $protocol, */ int $id, string $buffer) {
        parent::__construct($buffer);
        // $this->protocol = $protocol;
        $this->id = $id;
    }

    // public function getPacketProtocol() {
    //     return $this->protocol;
    // }

    public function getPacketId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRawBuffer(): string {
        return $this->buffer;
    }
}

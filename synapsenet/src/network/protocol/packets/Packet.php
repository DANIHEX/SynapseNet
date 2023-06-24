<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol\packets;

use synapsenet\binary\Buffer;

class Packet extends Buffer {

    // private int $protocol; TODO: Future use for converting packet protocol
    private int $id;
    protected bool $ready = false;

    public function __construct(/* int $protocol, */int $id, string $buffer){
        parent::__construct($buffer);
        // $this->protocol = $protocol;
        $this->id = $id;
    }

    // public function getPacketProtocol(){
    //     return $this->protocol;
    // }

    public function getPacketId(){
        return $this->id;
    }

    public function getRawBuffer(): string {
        return $this->buffer;
    }

}

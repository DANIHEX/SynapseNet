<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use synapsenet\network\protocol\Packet;

class ACK extends Packet {

    /**
     * @param int $id
     * @param string $buffer
     */
    public function __construct(int $id, string $buffer){
        parent::__construct($id, $buffer);
    }

    /**
     * @return $this
     */
    public function extract(): ACK {
        return $this;
    }

    /**
     * @return string
     */
    public function make(): string {
        $buffer = "";
        return $buffer;
    }

}
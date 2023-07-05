<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use Exception;
use synapsenet\binary\Buffer;
use synapsenet\network\protocol\Packet;

class UnknownPacket extends Packet {

    /**
     * @param string $buf
     * @throws Exception
     */
    public function __construct(string $buf) {
        parent::__construct(ord($buf[0]), $buf);
    }

    /**
     * @return $this
     */
    public function extract(): UnknownPacket {
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

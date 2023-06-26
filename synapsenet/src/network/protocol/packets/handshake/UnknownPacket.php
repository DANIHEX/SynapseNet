<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\binary\Buffer;
use synapsenet\network\protocol\packets\Packet;

class UnknownPacket extends Packet {

    /**
     * @param string $buf
     */
    public function __construct(string $buf) {
        $buffer = new Buffer($buf);
        parent::__construct(ord($buffer->get(1)), $buf);
    }

}

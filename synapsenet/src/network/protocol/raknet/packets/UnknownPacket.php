<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use synapsenet\binary\Buffer;
use synapsenet\network\protocol\raknet\RaknetPacket;

class UnknownPacket extends RaknetPacket {

    /**
     * @param string $buf
     */
    public function __construct(string $buf) {
        $buffer = new Buffer($buf);
        parent::__construct(ord($buffer->get(1)), $buf);
    }

}

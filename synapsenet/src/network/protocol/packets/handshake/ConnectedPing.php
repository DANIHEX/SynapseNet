<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\packets\Packet;
use synapsenet\network\protocol\packets\PacketIdentifiers;
use synapsenet\network\protocol\packets\PacketRead;

class ConnectedPing extends Packet implements PacketRead {

    /** @var int */
    private int $packetId = PacketIdentifiers::CONNECTED_PING;

    /** @var int */
    public int $time;

    /**
     * @param string $buffer
     */
    public function __construct(string $buffer) {
        parent::__construct($this->packetId, $buffer);
        $this->extract();
    }

    /**
     * @return int
     */
    public function getTime(): int {
        return $this->time;
    }

    /**
     * @return UnconnectedPing
     */
    public function extract(): ConnectedPing {
        $this->get(1);
        $this->time = Binary::readLong($this->get(8));

        return $this;
    }
}

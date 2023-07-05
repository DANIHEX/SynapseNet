<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use Exception;
use synapsenet\binary\Binary;
use synapsenet\network\protocol\Packet;
use synapsenet\network\protocol\raknet\RaknetPacketIds;

class ConnectedPing extends Packet {

    /** @var int */
    private int $packetId = RaknetPacketIds::CONNECTED_PING;

    /** @var int */
    public int $time;

    /**
     * @param string $buffer
     * @throws Exception
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
     * @return ConnectedPing
     * @throws Exception
     */
    public function extract(): ConnectedPing {
        $this->get(1);
        $this->time = Binary::readLong($this->get(8));

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

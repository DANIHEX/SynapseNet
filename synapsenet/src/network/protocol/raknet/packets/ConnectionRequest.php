<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use Exception;
use synapsenet\binary\Binary;
use synapsenet\network\protocol\Packet;
use synapsenet\network\protocol\raknet\RaknetPacketIds;

class ConnectionRequest extends Packet {

    /** @var int */
    private int $packetId = RaknetPacketIds::CONNECTION_REQUEST;

    /** @var int */
    public int $guid;

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
    public function getGuid(): int {
        return $this->guid;
    }

    /**
     * @return int
     */
    public function getTime(): int {
        return $this->time;
    }

    /**
     * @return ConnectionRequest
     * @throws Exception
     */
    public function extract(): ConnectionRequest {
        $this->get(1);
        $this->guid = Binary::readLong($this->get(8));
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

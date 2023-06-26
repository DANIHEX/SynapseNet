<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\packets\Packet;
use synapsenet\network\protocol\packets\PacketIdentifiers;
use synapsenet\network\protocol\packets\PacketRead;

class UnconnectedPing extends Packet implements PacketRead {

    /**
     * PacketIdentifiers::UNCONNECTED_PING_OPEN ??
     * 
     * @var int
     */
    private int $packetId = PacketIdentifiers::UNCONNECTED_PING;

    /** @var int */
    public int $time;

    /** @var string */
    public string $magic;

    /** @var int */
    public int $clientUid;

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
     * @return string
     */
    public function getMagic(): string {
        return $this->magic;
    }

    /**
     * @return int
     */
    public function getClientUid(): int {
        return $this->clientUid;
    }

    /**
     * @return UnconnectedPing
     */
    public function extract(): UnconnectedPing {
        $this->get(1);
        $this->time = Binary::readLong($this->get(8));
        $this->magic = $this->get(16);
        $this->clientUid = Binary::readLong($this->get(8));

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\network\protocol\packets\Packet;
use synapsenet\network\protocol\packets\PacketIdentifiers;
use synapsenet\network\protocol\packets\PacketRead;

class OpenConnectionRequest1 extends Packet implements PacketRead {

    /** @var int */
    private int $packetId = PacketIdentifiers::OPEN_CONNECTION_REQUEST_1;

    /** @var int */
    public int $protocol;

    /** @var int */
    public int $mtuSize;

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
    public function getProtocol(): int {
        return $this->protocol;
    }

    /**
     * @return int
     */
    public function getMtuSize(): int {
        return $this->mtuSize;
    }

    /**
     * @return OpenConnectionRequest1
     */
    public function extract(): OpenConnectionRequest1 {
        $this->get(1);
        $this->protocol = ord($this->get(1));
        $this->mtuSize = strlen($this->getRemaining()) + 46;

        return $this;
    }
}

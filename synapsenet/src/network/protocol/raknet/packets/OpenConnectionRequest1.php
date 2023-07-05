<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use Exception;
use synapsenet\network\protocol\Packet;
use synapsenet\network\protocol\raknet\RaknetPacketIds;

class OpenConnectionRequest1 extends Packet {

    /** @var int */
    private int $packetId = RaknetPacketIds::OPEN_CONNECTION_REQUEST_1;

    /** @var int */
    public int $protocol;

    /** @var int */
    public int $mtuSize;

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
     * @throws Exception
     */
    public function extract(): OpenConnectionRequest1 {
        $this->get(1);
        $this->protocol = ord($this->get(1));
        $this->mtuSize = strlen($this->getRemaining()) + 46;

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

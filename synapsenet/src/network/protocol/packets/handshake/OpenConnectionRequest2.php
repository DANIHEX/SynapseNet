<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\binary\Binary;
use synapsenet\network\Address;
use synapsenet\network\protocol\packets\Packet;
use synapsenet\network\protocol\packets\PacketIdentifiers;
use synapsenet\network\protocol\packets\PacketRead;

class OpenConnectionRequest2 extends Packet implements PacketRead {

    /** @var int */
    private int $packetId = PacketIdentifiers::OPEN_CONNECTION_REQUEST_2;

    /** @var Address */
    public Address $serverAddress;

    /** @var int */
    public int $mtuSize;

    /** @var int */
    public int $clientGuid;

    /**
     * @param string $buffer
     */
    public function __construct(string $buffer) {
        parent::__construct($this->packetId, $buffer);

        $this->extract();
    }

    /**
     * @return Address
     */
    public function getServerAddress(): Address {
        return $this->serverAddress;
    }

    /**
     * @return int
     */
    public function getMtuSize(): int {
        return $this->mtuSize;
    }

    /**
     * @return int
     */
    public function getClientGuid(): int {
        return $this->clientGuid;
    }

    /**
     * @return UnconnectedPing
     */
    public function extract(): OpenConnectionRequest2 {
        $this->get(1);
        $this->serverAddress = $this->getAddress($this->get(7));
        $this->mtuSize = Binary::readShort($this->get(2));
        $this->clientGuid = Binary::readLong($this->get(8));

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use synapsenet\network\Address;
use synapsenet\network\protocol\raknet\RaknetPacket;
use synapsenet\network\protocol\raknet\RaknetPacketIds;

class NewIncomingAddress extends RaknetPacket {

    /** @var int */
    private int $packetId = RaknetPacketIds::NEW_INCOMING_CONNECTION;

    /** @var Address */
    public Address $serverAddress;

    /** @var Address */
    public Address $internalAddress;

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
     * @return Address
     */
    public function getInternalAddress(): Address {
        return $this->serverAddress;
    }

    /**
     * @return NewIncomingAddress
     */
    public function extract(): NewIncomingAddress {
        $this->get(1);
        $this->serverAddress = $this->getAddress($this->get(7));
        $this->internalAddress = $this->getAddress($this->get(7));

        return $this;
    }
}

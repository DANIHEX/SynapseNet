<?php

namespace synapsenet\network\protocol\raknet\packets;

use Exception;
use synapsenet\network\Address;
use synapsenet\network\protocol\Packet;
use synapsenet\network\protocol\raknet\RaknetPacketIds;

class NewIncomingConnection extends Packet {

    /** @var int */
    private int $packetId = RaknetPacketIds::NEW_INCOMING_CONNECTION;

    /** @var Address */
    public Address $serverAddress;

    /** @var Address */
    public Address $internalAddress;

    /**
     * @param string $buffer
     * @throws Exception
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
     * @return NewIncomingConnection
     * @throws Exception
     */
    public function extract(): NewIncomingConnection {
        $this->get(1);
        $this->serverAddress = $this->getAddress($this->get(7));
        $this->internalAddress = $this->getAddress($this->get(7));

        return $this;
    }

    public function make(): string {
        $buffer = "";
        return $buffer;
    }

}
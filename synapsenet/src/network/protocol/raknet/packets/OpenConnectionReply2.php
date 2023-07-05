<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use synapsenet\binary\Binary;
use synapsenet\network\Address;
use synapsenet\network\protocol\Packet;
use synapsenet\network\protocol\raknet\RaknetPacketIds;

class OpenConnectionReply2 extends Packet {

    /** @var int */
    private int $packetId = RaknetPacketIds::OPEN_CONNECTION_REPLY_2;

    /** @var int */
    public int $serverGuid;

    /** @var Address */
    public Address $clientAddress;

    /** @var int */
    public int $mtuSize;

    /** @var bool */
    public bool $encryptionEnabled;

    public function __construct() {
        parent::__construct($this->packetId, "");
    }

    /**
     * @return $this
     */
    public function extract(): OpenConnectionReply2 {
        return $this;
    }

    /**
     * @return string
     */
    public function make(): string {
        $this->buffer .= chr($this->getPacketId());
        $this->buffer .= $this->magic;
        $this->buffer .= Binary::writeLong($this->serverGuid);
        $this->buffer .= $this->getAddressBuffer($this->clientAddress);
        $this->buffer .= Binary::writeShort($this->mtuSize);
        $this->buffer .= Binary::writeBool($this->encryptionEnabled);

        return $this->buffer;
    }
}

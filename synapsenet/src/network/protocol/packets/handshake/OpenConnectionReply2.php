<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\binary\Binary;
use synapsenet\network\Address;
use synapsenet\network\protocol\packets\Packet;
use synapsenet\network\protocol\packets\PacketIdentifiers;
use synapsenet\network\protocol\packets\PacketWrite;

class OpenConnectionReply2 extends Packet implements PacketWrite {

    /** @var int */
    private int $packetId = PacketIdentifiers::OPEN_CONNECTION_REPLY_2;

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

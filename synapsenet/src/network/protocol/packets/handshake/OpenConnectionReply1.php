<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\packets\Packet;
use synapsenet\network\protocol\packets\PacketIdentifiers;
use synapsenet\network\protocol\packets\PacketWrite;

class OpenConnectionReply1 extends Packet implements PacketWrite {

    /** @var int */
    private int $packetId = PacketIdentifiers::OPEN_CONNECTION_REPLY_1;

    /** @var int */
    public int $serverGuid;

    /** @var bool */
    public bool $useSecurity;

    /** @var int */
    public int $mtuSize;

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
        $this->buffer .= Binary::writeBool($this->useSecurity);
        $this->buffer .= Binary::writeShort($this->mtuSize);

        return $this->buffer;
    }
}

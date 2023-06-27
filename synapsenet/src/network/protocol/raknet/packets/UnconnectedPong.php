<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\raknet\RaknetPacket;
use synapsenet\network\protocol\raknet\RaknetPacketIds;

class UnconnectedPong extends RaknetPacket {

    /** @var int */
    private int $packetId = RaknetPacketIds::UNCONNECTED_PONG;

    /** @var int */
    public int $time;

    /** @var int */
    public int $serverGuid;

    /** @var string */
    public string $serverIdString;

    public function __construct() {
        parent::__construct($this->packetId, "");
    }

    /**
     * @return string
     */
    public function make(): string {
        $this->buffer .= chr($this->getPacketId());
        $this->buffer .= Binary::writeLong($this->time);
        $this->buffer .= Binary::writeLong($this->serverGuid);
        $this->buffer .= $this->magic;
        $this->buffer .= Binary::writeShort(strlen($this->serverIdString));
        $this->buffer .= $this->serverIdString;

        return $this->buffer;
    }
}

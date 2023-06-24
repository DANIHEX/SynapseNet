<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\packets\Packet;
use synapsenet\network\protocol\packets\PacketSendInterface;

class UnconnectedPong extends Packet implements PacketSendInterface {

    /** @var int */
    public int $time;
    /** @var int */
    public int $serverGuid;

    /** @var string */
    public string $serverIdString;

    /** @var int */
    private int $packetId = 0x1c;

    /** @var string */
    private string $magic = "00ffff00fefefefefdfdfdfd12345678";

    public function __construct() {
        parent::__construct($this->packetId, "");
    }

    /**
     * @return string
     */
    public function make(): string {
        $this->buffer .= $this->getPacketId();
        $this->buffer .= Binary::writeLong($this->time);
        $this->buffer .= Binary::writeLong($this->serverGuid);
        $this->buffer .= $this->magic;
        $this->buffer .= $this->serverIdString;
        $this->ready = true;

        return $this->buffer;
    }
}

<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\packets\Packet;

class UnconnectedPong extends Packet implements PacketSendInterface {

    private int $packetId = 0x1c;
    public int $time;
    public int $serverGuid;
    private string $magic = "00ffff00fefefefefdfdfdfd12345678";
    public string $serverIdString;

    public function __construct(){
        parent::__construct($this->packetId, "");
    }

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

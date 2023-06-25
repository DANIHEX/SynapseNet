<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\packets\Packet;
use synapsenet\network\protocol\packets\PacketSendInterface;

class UnconnectedPong extends Packet implements PacketSendInterface {

    /** @var int */
    private int $packetId = 0x1c;

    /** @var int */
    public int $time;

    /** @var int */
    public int $serverGuid;

    /** @var string */
    private string $magic = "\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78";

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
        $this->ready = true;

        return $this->buffer;
    }
}

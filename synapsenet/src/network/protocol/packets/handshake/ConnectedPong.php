<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\packets\Packet;
use synapsenet\network\protocol\packets\PacketIdentifiers;
use synapsenet\network\protocol\packets\PacketWrite;

class ConnectedPong extends Packet implements PacketWrite {

    /** @var int */
    private int $packetId = PacketIdentifiers::CONNECTED_PONG;

    /** @var int */
    public int $pingTtime;

    /** @var int */
    public int $pongTtime;

    public function __construct() {
        parent::__construct($this->packetId, "");
    }

    /**
     * @return string
     */
    public function make(): string {
        $this->buffer .= chr($this->getPacketId());
        $this->buffer .= Binary::writeLong($this->pingTtime);
        $this->buffer .= Binary::writeLong($this->pongTtime);

        return $this->buffer;
    }
}

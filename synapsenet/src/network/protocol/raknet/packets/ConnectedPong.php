<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\Packet;
use synapsenet\network\protocol\raknet\RaknetPacketIds;

class ConnectedPong extends Packet {

    /** @var int */
    private int $packetId = RaknetPacketIds::CONNECTED_PONG;

    /** @var int */
    public int $pingTime;

    /** @var int */
    public int $pongTime;

    public function __construct() {
        parent::__construct($this->packetId, "");
    }

    /**
     * @return $this
     */
    public function extract(): ConnectedPong {
        return $this;
    }

    /**
     * @return string
     */
    public function make(): string {
        $this->buffer .= chr($this->getPacketId());
        $this->buffer .= Binary::writeLong($this->pingTime);
        $this->buffer .= Binary::writeLong($this->pongTime);

        return $this->buffer;
    }
}

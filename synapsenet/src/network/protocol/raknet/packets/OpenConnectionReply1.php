<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\raknet\RaknetPacket;
use synapsenet\network\protocol\raknet\RaknetPacketIds;

class OpenConnectionReply1 extends RaknetPacket {

    /** @var int */
    private int $packetId = RaknetPacketIds::OPEN_CONNECTION_REPLY_1;

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

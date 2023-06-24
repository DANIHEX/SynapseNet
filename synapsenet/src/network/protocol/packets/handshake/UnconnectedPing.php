<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\packets\Packet;

class UnconnectedPing extends Packet implements PacketReciveInterface {

    private int $packetId = 0x01; // And 0x02
    public int $time;
    public string $magic;
    public int $clientUid;

    public function __construct(string $buffer){
        parent::__construct($this->packetId, $buffer);
        $this->extract();
    }

    public function getTime(): int {
        if(!$this->ready){
            $this->extract();
        }
        return $this->time;
    }

    public function getMagic(): string {
        if(!$this->ready){
            $this->extract();
        }
        return $this->magic;
    }

    public function getClientUid(): int {
        if(!$this->ready){
            $this->extract();
        }
        return $this->clientUid;
    }

    public function getDataArray(): array {
        if(!$this->ready){
            $this->extract();
        }
        return [
            $$this->time,
            $$this->magic,
            $$this->clientUid
        ];
    }

    public function extract(): UnconnectedPing {
        $this->time = Binary::readLong($this->get(8));
        $this->magic = $this->get(16);
        $this->clientUid = Binary::readLong($this->get(8));
        $this->ready = true;
        return $this;
    }

}

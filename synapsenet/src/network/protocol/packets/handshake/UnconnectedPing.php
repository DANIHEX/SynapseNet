<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets\handshake;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\packets\Packet;
use synapsenet\network\protocol\packets\PacketReciveInterface;

class UnconnectedPing extends Packet implements PacketReciveInterface {

    /** @var int */
    public int $time; // And 0x02

    /** @var string */
    public string $magic;

    /** @var int */
    public int $clientUid;
    /** @var int */
    private int $packetId = 0x01;

    /**
     * @param string $buffer
     */
    public function __construct(string $buffer) {
        parent::__construct($this->packetId, $buffer);

        $this->extract();
    }

    /**
     * @return UnconnectedPing
     */
    public function extract(): UnconnectedPing {
        $this->time = Binary::readLong($this->get(8));
        $this->magic = $this->get(16);
        $this->clientUid = Binary::readLong($this->get(8));
        $this->ready = true;

        return $this;
    }

    /**
     * @return int
     */
    public function getTime(): int {
        if(!$this->ready) {
            $this->extract();
        }

        return $this->time;
    }

    /**
     * @return string
     */
    public function getMagic(): string {
        if(!$this->ready) {
            $this->extract();
        }

        return $this->magic;
    }

    /**
     * @return int
     */
    public function getClientUid(): int {
        if(!$this->ready) {
            $this->extract();
        }

        return $this->clientUid;
    }

    /**
     * @return array
     */
    public function getDataArray(): array {
        if(!$this->ready) {
            $this->extract();
        }

        return [
            $this->time,
            $this->magic,
            $this->clientUid
        ];
    }
}

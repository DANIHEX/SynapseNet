<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use Exception;
use synapsenet\binary\Binary;
use synapsenet\network\protocol\Packet;

class FrameSetPacket extends Packet {

    /** @var int */
    public int $protocol;

    /** @var int */
    public int $mtuSize;

    /**
     * @var int
     */
    public int $sequenceNumber;

    /**
     * @param int $id
     * @param string $buffer
     * @throws Exception
     */
    public function __construct(int $id, string $buffer) {
        parent::__construct($id, $buffer);

        $this->extract();
    }

    /**
     * @return int
     */
    public function getProtocol(): int {
        return $this->protocol;
    }

    /**
     * @return int
     */
    public function getMtuSize(): int {
        return $this->mtuSize;
    }

    /**
     * @return FrameSetPacket
     * @throws Exception
     */
    public function extract(): FrameSetPacket {
        $this->get(1);
        $this->sequenceNumber = Binary::readLTriad($this->get(3));
        $flags = decbin(ord($this->get(1)));

        return $this;
    }

    public function make(): string {
        $buffer = "";
        return $buffer;
    }

    private function getReliablityType(string $bits): int {
        return match ($bits) {
            "000" => 0,
            "011" => 1,
            "100" => 2,
            "110" => 3,
            "111" => 4,
            "" => 5,
            "" => 6,
            "" => 7
        };
    }
}

<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use synapsenet\binary\Binary;
use synapsenet\network\protocol\raknet\RaknetPacket;

use function PHPSTORM_META\map;

class FrameSetPacket extends RaknetPacket {

    /** @var int */
    private int $packetId; // Range: 0x80 to 0x8d

    /** @var int */
    public int $protocol;

    /** @var int */
    public int $mtuSize;

    /**
     * @param string $buffer
     */
    public function __construct(int $packetId, string $buffer) {
        $this->packetId = $packetId;
        parent::__construct($this->packetId, $buffer);

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
     */
    public function extract(): FrameSetPacket {
        $this->get(1);
        $this->sequenceNumber = Binary::readLTriad($this->get(3));
        $flags = decbin(ord($this->get(1)));

        return $this;
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
        }
    }
}

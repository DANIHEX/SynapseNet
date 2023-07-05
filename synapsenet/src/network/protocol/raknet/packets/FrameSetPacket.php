<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use Exception;
use synapsenet\binary\Binary;
use synapsenet\network\protocol\Packet;
use synapsenet\network\protocol\raknet\ReliabilityType;

class FrameSetPacket extends Packet {

    /**
     * @var int
     */
    public int $sequenceNumber;

    /**
     * @var bool
     */
    public bool $reliable;
    /**
     * @var bool
     */
    public bool $ordered;
    /**
     * @var bool
     */
    public bool $sequenced;
    /**
     * @var bool
     */
    public bool $fragmented;

    /**
     * @var int
     */
    public int $length;

    /**
     * @var int
     */
    public int $reliableFrameIndex;
    /**
     * @var int
     */
    public int $sequencedFrameIndex;


    /**
     * @var array
     */
    public array $order = [];
    /**
     * @var array
     */
    public array $fragment = [];

    /**
     * @var string
     */
    public string $body;

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
    public function getSequenceNumber(): int {
        return $this->sequenceNumber;
    }

    /**
     * @return bool
     */
    public function isReliable(): bool {
        return $this->reliable;
    }

    /**
     * @return bool
     */
    public function isSOrdered(): bool {
        return $this->ordered;
    }

    /**
     * @return bool
     */
    public function isFragmented(): bool {
        return $this->fragmented;
    }

    /**
     * @return bool
     */
    public function isSequenced(): bool {
        return $this->sequenced;
    }

    /**
     * @return int
     */
    public function getLength(): int {
        return $this->length;
    }

    /**
     * @return int
     */
    public function getReliableFrameIndex(): int {
        return $this->reliableFrameIndex;
    }

    /**
     * @return int
     */
    public function getSequencedFrameIndex(): int {
        return $this->sequencedFrameIndex;
    }

    /**
     * @return array
     */
    public function getOrder(): array {
        return $this->order;
    }

    /**
     * @return array
     */
    public function getFragment(): array {
        return $this->fragment;
    }

    /**
     * @return string
     */
    public function getBody(): string {
        return $this->body;
    }

    /**
     * @return FrameSetPacket
     * @throws Exception
     */
    public function extract(): FrameSetPacket {
        $this->get(1);
        $this->sequenceNumber = Binary::readLTriad($this->get(3));
        $flags = $this->get(1);
        $this->length = Binary::readShort($this->get(2));
        if(ReliabilityType::reliable($flags)){
            $this->reliableFrameIndex = Binary::readLTriad($this->get(3));
        }
        if(ReliabilityType::ordered($flags)){
            $this->sequencedFrameIndex = Binary::readLTriad($this->get(3));
        }
        if(ReliabilityType::sequenced($flags)){
            $this->order[0] = Binary::readLTriad($this->get(3));
            $this->order[1] = $this->get(1);
        }
        if((($flags >> 4) & 0b0001) === 1){
            $this->fragment[0] = Binary::readInt($this->get(4));
            $this->fragment[1] = Binary::readShort($this->get(2));
            $this->fragment[2] = Binary::readInt($this->get(4));
        }
        $this->body = $this->get(intval(ceil($this->length / 8)));

        return $this;
    }

    public function make(): string {
        $buffer = "";
        return $buffer;
    }

}

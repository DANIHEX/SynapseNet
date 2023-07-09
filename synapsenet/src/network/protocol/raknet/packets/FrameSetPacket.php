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
     * @var int
     */
    public int $flags;

    /**
     * @var bool
     */
    public bool $reliable = false;
    /**
     * @var bool
     */
    public bool $ordered = false;
    /**
     * @var bool
     */
    public bool $sequenced = false;
    /**
     * @var bool
     */
    public bool $fragmented = false;

    /**
     * @var int
     */
    public int $length;

    /**
     * @var int
     */
    public int $reliableFrameIndex = 0;
    /**
     * @var int
     */
    public int $sequencedFrameIndex = 0;
    /**
     * @var int
     */
    public int $orderFrameIndex;
    /**
     * @var int
     */
    public int $orderChannel;
    /**
     * @var int
     */
    public int $fragmentCompoundSize;
    /**
     * @var int
     */
    public int $fragmentCompoundId;
    /**
     * @var int
     */
    public int $fragmentIndex;

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
    }

    /**
     * @return int
     */
    public function getSequenceNumber(): int {
        return $this->sequenceNumber;
    }

    /**
     * @return int
     */
    public function getFlags(): int {
        return $this->flags;
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
    public function isOrdered(): bool {
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
     * @return int
     */
    public function getOrderFrameIndex(): int {
        return $this->orderFrameIndex;
    }

    /**
     * @return int
     */
    public function getOrderChannel(): int {
        return $this->orderChannel;
    }

    /**
     * @return int
     */
    public function getFragmentCompoundSize(): int {
        return $this->fragmentCompoundSize;
    }

    /**
     * @return int
     */
    public function getFragmentCompoundId(): int {
        return $this->fragmentCompoundId;
    }

    /**
     * @return int
     */
    public function getFragmentIndex(): int {
        return $this->fragmentIndex;
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
        $this->flags = $flags = ord($this->get(1));
        $this->length = Binary::readShort($this->get(2));
        if(ReliabilityType::reliable($flags)){
            $this->reliable = true;
            $this->reliableFrameIndex = Binary::readLTriad($this->get(3));
        }
        if(ReliabilityType::sequenced($flags)){
            $this->sequenced = true;
            $this->sequencedFrameIndex = Binary::readLTriad($this->get(3));
        }
        if(ReliabilityType::ordered($flags)){
            $this->ordered = true;
            $this->orderFrameIndex = Binary::readLTriad($this->get(3));
            $this->orderChannel = ord($this->get(1));
        }
        if((($flags >> 4) & 0b0001) === 1){
            $this->fragmented = true;
            $this->fragmentCompoundSize = Binary::readInt($this->get(4));
            $this->fragmentCompoundId = Binary::readShort($this->get(2));
            $this->fragmentIndex = Binary::readInt($this->get(4));
        }
        $this->body = $this->get(intval(ceil($this->length / 8)));

        return $this;
    }

    public function make(): string {
        $buffer = chr($this->getPacketId());
        $buffer .= Binary::writeLTriad($this->sequenceNumber);
        $buffer .= Binary::writeByte($this->flags);
        $buffer .= Binary::writeShort(strlen($buffer) << 3);
        if(ReliabilityType::reliable($this->flags)){
            $buffer .= Binary::writeLTriad($this->reliableFrameIndex);
        }
        if(ReliabilityType::sequenced($this->flags)){
            $buffer .= Binary::writeLTriad($this->sequencedFrameIndex);
        }
        if(ReliabilityType::ordered($this->flags)){
            $buffer .= Binary::writeLTriad($this->orderFrameIndex);
            $buffer .= chr($this->orderChannel);
        }
        if((($this->flags >> 4) & 0b0001) === 1){
            $this->fragmented = true;
            $buffer .= Binary::writeInt($this->fragmentCompoundSize);
            $buffer .= Binary::writeShort($this->fragmentCompoundId);
            $buffer .= Binary::writeInt($this->fragmentIndex);
        }
        $buffer .= $this->buffer;
        $this->buffer = $buffer;
        return $buffer;
    }

}

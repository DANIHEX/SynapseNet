<?php

namespace synapsenet\binary;

use RuntimeException;

class NBTBEBinaryStream {

    /** @var string */
    private string $buffer;

    /** @var int */
    private int $offset;

    /**
     * @param string $buffer
     */
    public function __construct(string $buffer) {
        $this->buffer = $buffer;
        $this->offset = 0;
    }

    /**
     * Reads the root tag from the stream.
     *
     * @return NBTTag
     */
    public function readRootTag(): NBTTag {
        $type = $this->readByte();
        if($type !== 10) { // Tag_Compound
            throw new RuntimeException('Invalid NBT format: root tag must be a compound tag.');
        }

        $name = $this->readString();
        $value = [];
        while(true) {
            $tagType = $this->readByte();
            if($tagType === 0) { // Tag_End
                break;
            }

            $tagName = $this->readString();
            $tagValue = $this->readTagValue($tagType);
            $value[] = new NBTTag($tagType, $tagName, $tagValue);
        }

        return new NBTTag($type, $name, $value);
    }

    /**
     * Reads the next string from the stream.
     *
     * @return string
     */
    private function readString(): string {
        $length = $this->readShort();
        if($length === 0) {
            return '';
        }

        $string = substr($this->buffer, $this->offset, $length);
        $this->offset += $length;

        return $string;
    }

    /**
     * Reads the next short from the stream.
     *
     * @return int
     */
    private function readShort(): int {
        $value = ($this->readByte() << 8) | $this->readByte();
        if($value > 0x7FFF) {
            $value = $value - 0x10000;
        }

        return $value;
    }

    /**
     * Reads the value of a tag based on its type.
     *
     * @param int $type
     *
     * @return string|array|int|float
     */
    private function readTagValue(int $type): string|array|int|float {
        return match ($type) {
            1 => $this->readByte(),
            2 => $this->readShort(),
            3 => $this->readInt(),
            4 => $this->readLong(),
            5 => $this->readFloat(),
            6 => $this->readDouble(),
            7 => $this->readByteArray(),
            8 => $this->readString(),
            9 => $this->readList(),
            10 => $this->readCompound(),
            11 => $this->readIntArray(),
            12 => $this->readLongArray(),
            13 => $this->readDoubleArray(),
            default => throw new RuntimeException("Invalid NBT format: unknown tag type $type."),
        };
    }

    /**
     * Reads the next float from the stream.
     *
     * @return float
     */
    private function readFloat(): float {
        $bits = $this->readInt();

        return unpack('f', pack('i', $bits))[1];
    }

    /**
     * Reads the next int from the stream.
     *
     * @return int
     */
    private function readInt(): int {
        $value = 0;
        for($i = 0; $i < 4; ++$i) {
            $value = ($value << 8) | $this->readByte();
        }

        if($value > 0x7FFFFFFF) {
            $value = $value - 0x100000000;
        }

        return $value;
    }

    /**
     * Reads the next byte from the stream.
     *
     * @return int
     */
    private function readByte(): int {
        $byte = ord($this->buffer[$this->offset]);
        ++$this->offset;

        return $byte;
    }

    /**
     * Reads the next double from the stream.
     *
     * @return float
     */
    private function readDouble(): float {
        $bits = $this->readLong();

        return unpack('d', pack('q', $bits))[1];
    }

    /**
     * Reads the next long from the stream.
     *
     * @return float
     */
    private function readLong(): float {
        $value = 0;
        for($i = 0; $i < 8; ++$i) {
            $value = ($value << 8) | $this->readByte();
        }

        if($value > 0x7FFFFFFFFFFFFFFF) {
            $value = $value - 0x10000000000000000;
        }

        return $value;
    }

    /**
     * Reads the next byte array from the stream.
     *
     * @return string
     */
    private function readByteArray(): string {
        $length = $this->readInt();
        if($length === 0) {
            return '';
        }

        $bytes = substr($this->buffer, $this->offset, $length);
        $this->offset += $length;

        return $bytes;
    }

    /**
     * Reads a list tag from the stream.
     *
     * @return array
     */
    private function readList(): array {
        $elementType = $this->readByte();
        $length = $this->readInt();
        $value = [];
        for($i = 0; $i < $length; ++$i) {
            $tagValue = $this->readTagValue($elementType);
            $value[] = $tagValue;
        }

        return $value;
    }

    /**
     * Reads a compound tag from the stream.
     *
     * @return array
     */
    private function readCompound(): array {
        $value = [];
        while(true) {
            $tagType = $this->readByte();
            if($tagType === 0) { // Tag_End
                break;
            }

            $tagName = $this->readString();
            $tagValue = $this->readTagValue($tagType);
            $value[$tagName] = $tagValue;
        }

        return $value;
    }

    /**
     * Reads an int array tag from the stream.
     *
     * @return array
     */
    private function readIntArray(): array {
        $length = $this->readInt();
        $value = [];
        for($i = 0; $i < $length; ++$i) {
            $intValue = $this->readInt();
            $value[] = $intValue;
        }

        return $value;
    }

    /**
     * Reads a long array tag from the stream.
     *
     * @return array
     */
    private function readLongArray(): array {
        $length = $this->readInt();
        $value = [];
        for($i = 0; $i < $length; ++$i) {
            $longValue = $this->readLong();
            $value[] = $longValue;
        }

        return $value;
    }

    /**
     * Reads a double array tag from the stream.
     *
     * @return array
     */
    private function readDoubleArray(): array {
        $length = $this->readInt();
        $value = [];
        for($i = 0; $i < $length; ++$i) {
            $doubleValue = $this->readDouble();
            $value[] = $doubleValue;
        }

        return $value;
    }
}

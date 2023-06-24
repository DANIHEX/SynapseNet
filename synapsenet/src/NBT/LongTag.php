<?php

namespace synapsenet\NBT;

use synapsenet\binary\DataReader;
use synapsenet\binary\DataWriter;

class LongTag extends Tag {

    /** @var int */
    private int $value;

    /**
     * @param string $name
     * @param int $value
     */
    public function __construct(string $name = "", int $value = 0) {
        parent::__construct($name);

        $this->value = $value;
    }

    /**
     * @param DataReader $reader
     *
     * @return void
     */
    public function read(DataReader $reader): void {
        $this->value = $reader->readLong();
    }

    /**
     * @param DataWriter $writer
     *
     * @return void
     */
    public function write(DataWriter $writer): void {
        $writer->writeLong($this->value);
    }

    /**
     * @return int
     */
    public function getValue(): int {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @return void
     */
    public function setValue(int $value): void {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getType(): int {
        return Tag::TAG_Long;
    }
}
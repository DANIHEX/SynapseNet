<?php

namespace synapsenet\NBT;

use synapsenet\binary\DataReader;
use synapsenet\binary\DataWriter;

class LongArrayTag extends Tag {

    /** @var array */
    private array $value;

    /**
     * @param string $name
     * @param array $value
     */
    public function __construct(string $name = "", array $value = []) {
        parent::__construct($name);

        $this->value = $value;
    }

    /**
     * @param DataReader $reader
     *
     * @return void
     */
    public function read(DataReader $reader): void {
        $this->value = $reader->readLongArray();
    }

    /**
     * @param DataWriter $writer
     *
     * @return void
     */
    public function write(DataWriter $writer): void {
        $writer->writeLongArray($this->value);
    }

    /**
     * @return array
     */
    public function getValue(): array {
        return $this->value;
    }

    /**
     * @param array $value
     *
     * @return void
     */
    public function setValue(array $value): void {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getType(): int {
        return Tag::TAG_LongArray;
    }
}
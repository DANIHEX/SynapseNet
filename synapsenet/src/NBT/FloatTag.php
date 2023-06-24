<?php

namespace synapsenet\NBT;

use synapsenet\binary\DataReader;
use synapsenet\binary\DataWriter;

class FloatTag extends Tag {

    /** @var float */
    private float $value;

    /**
     * @param string $name
     * @param float $value
     */
    public function __construct(string $name = "", float $value = 0.0) {
        parent::__construct($name);

        $this->value = $value;
    }

    /**
     * @param DataReader $reader
     *
     * @return void
     */
    public function read(DataReader $reader): void {
        $this->value = $reader->readFloat();
    }

    /**
     * @param DataWriter $writer
     *
     * @return void
     */
    public function write(DataWriter $writer): void {
        $writer->writeFloat($this->value);
    }

    /**
     * @return float
     */
    public function getValue(): float {
        return $this->value;
    }

    /**
     * @param float $value
     *
     * @return void
     */
    public function setValue(float $value): void {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getType(): int {
        return Tag::TAG_Float;
    }
}
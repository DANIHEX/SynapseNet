<?php

namespace synapsenet\NBT;

use synapsenet\binary\DataWriter;
use synapsenet\binary\DataReader;

class IntTag extends Tag {

    /** @var int */
    private int $value;

    /**
     * @param int $value
     */
    public function __construct(int $value) {
        parent::__construct(Tag::TAG_Int);

        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int {
        return $this->value;
    }

    /**
     * @param DataWriter $writer
     *
     * @return void
     */
    public function write(DataWriter $writer): void {
        $writer->writeInt($this->value);
    }

    /**
     * @return int
     */
    public function getType(): int {
        return Tag::TAG_Int;
    }

    /**
     * @param DataReader $reader
     *
     * @return void
     */
    public function read(DataReader $reader): void {
        $this->value = $reader->readInt();
    }
}

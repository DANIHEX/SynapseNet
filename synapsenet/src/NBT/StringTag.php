<?php

namespace synapsenet\NBT;

use synapsenet\binary\DataWriter;
use synapsenet\binary\DataReader;

class StringTag extends Tag {

    private string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value) {
        parent::__construct(Tag::TAG_String);

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string {
        return $this->value;
    }

    /**
     * @param DataWriter $writer
     *
     * @return void
     */
    public function write(DataWriter $writer): void {
        $writer->writeString($this->value);
    }

    /**
     * @return int
     */
    public function getType(): int {
        return Tag::TAG_String;
    }

    /**
     * @param DataReader $reader
     *
     * @return void
     */
    public function read(DataReader $reader): void {
        $this->value = $reader->readString();
    }
}

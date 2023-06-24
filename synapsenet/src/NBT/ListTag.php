<?php

namespace synapsenet\NBT;

use synapsenet\binary\DataWriter;
use synapsenet\binary\DataReader;

class ListTag extends Tag {

    /** @var int */
    private int $tagType;

    /** @var array */
    private array $value;

    /**
     * @param int $tagType
     * @param array $value
     */
    public function __construct(int $tagType, array $value) {
        parent::__construct(Tag::TAG_List);
        $this->tagType = $tagType;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->tagType;
    }

    /**
     * @return array
     */
    public function getValue(): array {
        return $this->value;
    }

    /**
     * @param DataWriter $writer
     *
     * @return void
     */
    public function write(DataWriter $writer): void {
        $writer->writeList($this->value);
    }

    /**
     * @param DataReader $reader
     *
     * @return void
     */
    public function read(DataReader $reader): void {
        $this->tagType = $reader->readByte();
        $this->value = [];
        $tagCount = $reader->readInt();
        for($i = 0; $i < $tagCount; $i++) {
            $tag = $reader->readTagByType($this->tagType);
            if($tag !== null) {
                $this->value[] = $tag;
            }
        }
    }
}

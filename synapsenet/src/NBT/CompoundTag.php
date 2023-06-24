<?php

namespace synapsenet\NBT;

use synapsenet\binary\DataReader;
use synapsenet\binary\DataWriter;

class CompoundTag extends Tag {

    /** @var array */
    private array $value = [];

    /**
     * @param DataReader $reader
     *
     * @return void
     */
    public function read(DataReader $reader): void {
        while(true) {
            $type = $reader->readByte();

            if($type === Tag::TAG_End) {
                break;
            }

            $name = $reader->readString();
            $tag = TagFactory::createTagInstance($type, $name);

            if($tag !== null) {
                $tag->read($reader);
                $this->value[] = $tag;
            }
        }
    }

    /**
     * @param DataWriter $writer
     *
     * @return void
     */
    public function write(DataWriter $writer): void {
        foreach($this->value as $tag) {
            $tag->write($writer);
        }

        $writer->writeByte(Tag::TAG_End);
    }

    /**
     * @param string $name
     *
     * @return Tag|null
     */
    public function getTag(string $name): ?Tag {
        foreach($this->value as $tag) {
            if($tag->getName() === $name) {
                return $tag;
            }
        }

        return null;
    }

    /**
     * @param Tag $tag
     *
     * @return void
     */
    public function setTag(Tag $tag): void {
        $this->value[] = $tag;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function removeTag(string $name): void {
        foreach($this->value as $index => $tag) {
            if($tag->getName() === $name) {
                unset($this->value[$index]);
                $this->value = array_values($this->value);
                break;
            }
        }
    }

    /**
     * @return int
     */
    public function getType(): int {
        return Tag::TAG_Compound;
    }
}

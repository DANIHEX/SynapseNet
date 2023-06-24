<?php

namespace synapsenet\NBT;

use synapsenet\binary\DataReader;
use synapsenet\binary\DataWriter;

abstract class Tag {

    public const TAG_End = 0;
    public const TAG_Byte = 1;
    public const TAG_Short = 2;
    public const TAG_Int = 3;
    public const TAG_Long = 4;
    public const TAG_Float = 5;
    public const TAG_Double = 6;
    public const TAG_ByteArray = 7;
    public const TAG_String = 8;
    public const TAG_List = 9;
    public const TAG_Compound = 10;
    public const TAG_IntArray = 11;
    public const TAG_LongArray = 12;

    /** @var string */
    protected string $name;

    /**
     * @param string $name
     */
    public function __construct(string $name = "") {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    abstract public function getType(): int;

    /**
     * @param DataReader $reader
     *
     * @return void
     */
    abstract public function read(DataReader $reader): void;

    /**
     * @param DataWriter $writer
     *
     * @return void
     */
    abstract public function write(DataWriter $writer): void;
}

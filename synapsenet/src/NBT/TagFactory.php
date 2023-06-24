<?php

namespace synapsenet\NBT;

class TagFactory {

    /**
     * @param int $type
     * @param string $name
     *
     * @return Tag|null
     */
    public static function createTagInstance(int $type, string $name): ?Tag {
        return match ($type) {
            Tag::TAG_Byte => new ByteTag($name),
            Tag::TAG_Short => new ShortTag($name),
            Tag::TAG_Int => new IntTag($name),
            Tag::TAG_Long => new LongTag($name),
            Tag::TAG_Float => new FloatTag($name),
            Tag::TAG_Double => new DoubleTag($name),
            Tag::TAG_ByteArray => new ByteArrayTag($name, []),
            Tag::TAG_String => new StringTag($name),
            Tag::TAG_List => new ListTag($name, []),
            Tag::TAG_Compound => new CompoundTag($name),
            Tag::TAG_IntArray => new IntArrayTag($name, []),
            Tag::TAG_LongArray => new LongArrayTag($name, []),
            default => null,
        };
    }
}

<?php

namespace synapsenet\network\protocol\raknet;

class ReliabilityType {

    public const FLAGS = 0b11100000;

    /**
     * @param $flags
     * @return bool
     */
    public static function reliable($flags): bool {
        $id = ($flags & self::FLAGS) >> 5;
        return (
            $id === 2    // Reliable
            or $id === 3 // Reliable Ordered
            or $id === 4 // Reliable Sequenced
            or $id === 6 // Reliable (+ACK)
            or $id === 7 // Reliable Ordered (+ACK)
        );
    }

    /**
     * @param $flags
     * @return bool
     */
    public static function ordered($flags): bool {
        $id = ($flags & self::FLAGS) >> 5;
        return (
            $id === 3    // Reliable Ordered
            or $id === 7 // Reliable Ordered (+ACK)
        );
    }

    /**
     * @param $flags
     * @return bool
     */
    public static function sequenced($flags): bool {
        $id = ($flags & self::FLAGS) >> 5;
        return (
            $id === 1    // Unreliable Sequenced
            or $id === 4 // Reliable Sequenced
        );
    }

}
<?php

// This is based on https://github.com/OculusVR/RakNet/blob/master/Source/PacketPriority.h

namespace synapsenet\network\protocol\raknet;

class ReliabilityType {

    public const MAP = [

    ];

    public const UNRELIABLE = 0;
    public const UNRELIABLE_SEQUENCED = 1;
    public const RELIABLE = 2;
    public const RELIABLE_ORDERED = 3;
    public const RELIABLE_SEQUENCED = 4;
    public const UNRELIABLE_ACK = 5;
    public const RELIABLE_ACK = 6;
    public const RELIABLE_ORDERED_ACK = 7;

    /**
     * @param int $id
     * @return bool
     */
    public static function reliable(int $id): bool {
        return (
            $id === self::RELIABLE
            or $id === self::RELIABLE_ORDERED
            or $id === self::RELIABLE_SEQUENCED
            or $id === self::RELIABLE_ACK
            or $id === self::RELIABLE_ORDERED_ACK
        );
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function ordered(int $id): bool {
        return (
            $id === self::RELIABLE_ORDERED    // Reliable Ordered
            or $id === self::RELIABLE_ORDERED_ACK // Reliable Ordered (+ACK)
        );
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function sequenced(int $id): bool {
        return (
            $id === self::UNRELIABLE_SEQUENCED    // Unreliable Sequenced
            or $id === self::RELIABLE_SEQUENCED // Reliable Sequenced
        );
    }

}
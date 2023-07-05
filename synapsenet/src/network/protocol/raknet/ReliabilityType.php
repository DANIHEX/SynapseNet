<?php

namespace synapsenet\network\protocol\raknet;

class ReliabilityType {

    /**
     * Reliable: 0b1(0b10000000)
     *
     * @param $flags
     * @return bool
     */
    public static function reliable($flags): bool {
        return ($flags >> 7) & 0b1 === 1;
    }

    /**
     * Ordered: 0b01(0b01000000)
     *
     * @param $flags
     * @return bool
     */
    public static function ordered($flags): bool {
        return ($flags >> 6) & 0b01 === 1;
    }

    /**
     * Sequenced: 0b001(0b00100000)
     *
     * @param $flags
     * @return bool
     */
    public static function sequenced($flags): bool {
        return ($flags >> 5) & 0b001 === 1;
    }

}
<?php

namespace synapsenet\level;

class CoordinateUtils {
    public static function hashXZ($x, $z) {
        return ($x & 0xffffffff) << 16 | ($z & 0xffffffff);
    }

    public static function unhashXZ($xz) {
        $x = ($xz >> 16) & 0xffffffff;
        $z = $xz & 0xffffffff;
        return [$x, $z];
    }
}

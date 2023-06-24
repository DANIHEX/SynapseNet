<?php

namespace synapsenet\event;

// ??
class Canceller {

    /** @var string[] */
    private static array $cancelled = [];

    /**
     * @param string $eventClass
     *
     * @return void
     */
    public static function cancel(string $eventClass): void {
        self::$cancelled[$eventClass] = true;
    }

    /**
     * @param string $eventClass
     *
     * @return void
     */
    public static function uncancel(string $eventClass): void {
        unset(self::$cancelled[$eventClass]);
    }

    /**
     * @param string $eventClass
     *
     * @return bool
     */
    public static function isCancelled(string $eventClass): bool {
        return isset(self::$cancelled[$eventClass]);
    }
}

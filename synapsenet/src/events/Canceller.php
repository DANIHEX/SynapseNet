<?php

namespace synapsenet\event;

class Canceller
{
    private static $cancelled = [];

    public static function cancel(string $eventClass): void
    {
        self::$cancelledEvents[$eventClass] = true;
    }

    public static function uncancel(string $eventClass): void
    {
        unset(self::$cancelledEvents[$eventClass]);
    }

    public static function isCancelled(string $eventClass): bool
    {
        return isset(self::$cancelledEvents[$eventClass]);
    }
}

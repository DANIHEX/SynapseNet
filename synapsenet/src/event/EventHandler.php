<?php

namespace synapsenet\event;

use Closure;

class EventHandler {

    /** @var Closure[][] */
    protected static array $eventHandlers = [];

    /**
     * @param string $eventName
     * @param Closure $handler
     *
     * @return void
     */
    public static function registerHandler(string $eventName, Closure $handler): void {
        if(!isset(self::$eventHandlers[$eventName])) {
            self::$eventHandlers[$eventName] = [];
        }

        self::$eventHandlers[$eventName][] = $handler;
    }

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function callEvent(Event $event): void {
        $eventName = $event->getEventName();

        if(isset(self::$eventHandlers[$eventName])) {
            foreach(self::$eventHandlers[$eventName] as $handler) {
                call_user_func($handler, $event);

                if($event->isCancelled()) {
                    break;
                }
            }
        }
    }
}

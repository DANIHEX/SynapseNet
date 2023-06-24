<?php

namespace synapsenet\event;

class EventHandler
{
    protected static $eventHandlers = [];
    
    public static function registerHandler(string $eventName, callable $handler): void
    {
        if (!isset(self::$eventHandlers[$eventName])) {
            self::$eventHandlers[$eventName] = [];
        }
        
        self::$eventHandlers[$eventName][] = $handler;
    }
    
    public static function callEvent(Event $event): void
    {
        $eventName = $event->getEventName();
        
        if (isset(self::$eventHandlers[$eventName])) {
            foreach (self::$eventHandlers[$eventName] as $handler) {
                call_user_func($handler, $event);
                
                if ($event->isCancelled()) {
                    break;
                }
            }
        }
    }
}

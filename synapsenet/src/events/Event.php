<?php

namespace synapsenet\event;

abstract class Event
{
    protected $cancelled = false;
    protected $listeners = [];
    
    public function isCancelled(): bool
    {
        return $this->cancelled;
    }
    
    public function setCancelled(bool $cancelled): void
    {
        $this->cancelled = $cancelled;
    }
    
    public function registerListener(callable $listener): void
    {
        $this->listeners[] = $listener;
    }
    
    public function call(): void
    {
        foreach ($this->listeners as $listener) {
            call_user_func($listener, $this);
            if ($this->isCancelled()) {
                break;
            }
        }
    }
    
    public function getEventName(): string
    {
        return static::class;
    }
}

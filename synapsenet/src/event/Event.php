<?php

namespace synapsenet\event;

use Closure;

abstract class Event {

    /** @var bool */
    protected bool $cancelled = false;

    /** @var Closure[] */
    protected array $listeners = [];

    /**
     * @param Closure $listener
     *
     * @return void
     */
    public function registerListener(Closure $listener): void {
        $this->listeners[] = $listener;
    }

    /**
     * @return void
     */
    public function call(): void {
        foreach($this->listeners as $listener) {
            call_user_func($listener, $this);
            if($this->isCancelled()) {
                break;
            }
        }
    }

    /**
     * @return bool
     */
    public function isCancelled(): bool {
        return $this->cancelled;
    }

    /**
     * @param bool $cancelled
     *
     * @return void
     */
    public function setCancelled(bool $cancelled): void {
        $this->cancelled = $cancelled;
    }

    /**
     * @return string
     */
    public function getEventName(): string {
        return static::class;
    }
}

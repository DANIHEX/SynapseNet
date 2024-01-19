<?php

namespace synapsenet\event\player;

use synapsenet\item\Item;

class PlayerChatEvent extends PlayerEvent {

    private $message;

    public function __construct(string $message) {
        $this->message = $message;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function setMessage(string $message) {
        $this->message = $message;
    }
}

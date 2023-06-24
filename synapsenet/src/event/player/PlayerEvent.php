<?php

namespace synapsenet\event\player;

use synapsenet\event\Event;
use synapsenet\player\Player;

abstract class PlayerEvent extends Event {

    /** @var Player */
    protected Player $player;

    /**
     * @param Player $player
     */
    public function __construct(Player $player) {
        $this->player = $player;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }
}

<?php 

namespace synapsenet\event\player;

use synapsenet\event\Event;
use synapsenet\player\Player;

abstract class PlayerEvent extends Event
{
    protected $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }
}

<?php

namespace synapsenet\player;

class Gamemode
{
    const SURVIVAL = 0;
    const CREATIVE = 1;
    const ADVENTURE = 2;
    const SPECTATOR = 3;

    protected $mode;

    public function __construct(int $mode = self::SURVIVAL)
    {
        $this->mode = $mode;
    }

    public function getMode(): int
    {
        return $this->mode;
    }

    public function setMode(int $mode): void
    {
        $this->mode = $mode;
    }
}

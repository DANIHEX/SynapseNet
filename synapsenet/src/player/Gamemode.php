<?php

namespace synapsenet\player;

class Gamemode {

    public const SURVIVAL = 0;
    public const CREATIVE = 1;
    public const ADVENTURE = 2;
    public const SPECTATOR = 3;

    /** @var int */
    protected int $mode;

    /**
     * @param int $mode
     */
    public function __construct(int $mode = self::SURVIVAL) {
        $this->mode = $mode;
    }

    /**
     * @return int
     */
    public function getMode(): int {
        return $this->mode;
    }

    /**
     * @param int $mode
     *
     * @return void
     */
    public function setMode(int $mode): void {
        $this->mode = $mode;
    }
}

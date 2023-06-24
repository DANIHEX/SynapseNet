<?php

namespace synapsenet\player;

class Player {

    /** @var string */
    protected string $name;
    /** @var string */
    protected string $nametag;
    /** @var string */
    private string $displayName;

    /** @var Gamemode */
    protected Gamemode $gamemode;

    // ?
    /** @var string */
    protected string $xuid;

    // No way yet to get uuid
    protected $uuid;

    /**
     * @param string $name
     */
    public function __construct(string $name) {
        $this->name = $name;
        $this->displayName = $name;
        $this->nametag = $name;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName): void {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getNametag(): string {
        return $this->nametag;
    }

    /**
     * @param string $nametag
     *
     * @return void
     */
    public function setNametag(string $nametag): void {
        $this->nametag = $nametag;
    }

    /**
     * @return Gamemode
     */
    public function getGamemode(): Gamemode {
        return $this->gamemode;
    }

    /**
     * @param Gamemode $gamemode
     *
     * @return void
     */
    public function setGamemode(Gamemode $gamemode): void {
        $this->gamemode = $gamemode;
    }

    /**
     * @return string
     */
    public function getXuid(): string {
        return $this->xuid;
    }

    /**
     * @param string $xuid
     *
     * @return void
     */
    public function setXuid(string $xuid): void {
        $this->xuid = $xuid;
    }
}

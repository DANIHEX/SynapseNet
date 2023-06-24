<?php 

namespace synapsenet\player;

class Player
{
    protected $name;
    protected $gamemode;

    protected string $nametag;
    // ?
    protected $xuid;
    // No way yet to get uuid
    protected $uuid;

    public function __construct(string $name)
    {
        $this->displayName = $name;
        $this->nametag = $name;
    }


    public function getNametag(): string
    {
        return $this->nametag;
    }

    public function setNametag(string $nametag): void
    {
        $this->nametag = $nametag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGamemode(): Gamemode
    {
        return $this->gamemode;
    }

    public function setGamemode(Gamemode $gamemode): void
    {
        $this->gamemode = $gamemode;
    }

    public function getXuid(): string
    {
        return $this->xuid;
    }

    public function setXuid(string $xuid): void
    {
        $this->xuid = $xuid;
    }
}

<?php

declare(strict_types = 1);

namespace synapsenet\network;

class Query {

    public string $string = "";

    public function __construct(){}

    public function generateQuery(string $name, int $onlinePlayers, int $maxPlayers, string $suid, string $motd, string $gamemode, int $gamemodeNumeric, int $port, int $port6): Query {
        $string = "MCPE;";
        $string .= $name . ";";
        $string .= "400;";
        $string .= "1.17-1.20;";
        $string .= $onlinePlayers . ";";
        $string .= $maxPlayers . ";";
        $string .= $suid . ";";
        $string .= $motd . ";";
        $string .= $gamemode . ";";
        $string .= $gamemodeNumeric . ";";
        $string .= $port . ";";
        $string .= $port6 . ";";
        $this->string = $string;
        return $this;
    }

    public function getQueryString(){
        return $this->string;
    }

}

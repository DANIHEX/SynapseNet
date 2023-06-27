<?php

declare(strict_types=1);

namespace synapsenet\network;

class Query {

    /** @var string */
    public string $string = "";

    /**
     * @param string $name
     * @param int $onlinePlayers
     * @param int $maxPlayers
     * @param string $suid
     * @param string $motd
     * @param string $gamemode
     * @param int $gamemodeNumeric
     * @param int $port
     * @param int $port6
     *
     * @return Query
     */
    public function generateQuery(string $name, int $onlinePlayers, int $maxPlayers, string $suid, string $motq, string $gamemode, int $gamemodeNumeric, int $port, int $port6): Query {
        $string = "MCPE;";
        $string .= $name . ";";
        $string .= "582;";
        $string .= "1.20;";
        $string .= $onlinePlayers . ";";
        $string .= $maxPlayers . ";";
        $string .= $suid . ";";
        $string .= $motq . ";";
        $string .= $gamemode . ";";
        $string .= 1 . ";"; // NOTE: No clue why this should be 1 to work. Am I dumb?
        $string .= $port . ";";
        $string .= $port6 . ";";
        $this->string = $string;

        return $this;
    }

    /**
     * @return string
     */
    public function getQueryString(): string {
        return $this->string;
    }
}

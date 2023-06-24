<?php

declare(strict_types = 1);

namespace synapsenet\network;

use synapsenet\core\CoreServer;
use synapsenet\network\protocol\packets\PacketHandler;
use synapsenet\network\protocol\ServerSocket;

class Network {

    public CoreServer $server;

    protected string $ip;
    protected string $ip6;
    protected int $port;
    protected int $port6;

    private PacketHandler $packetHandler;

    public function __construct(CoreServer $server, string $ip, string $ip6, int $port, int $port6){
        $this->server = $server;
        $this->ip = $ip;
        $this->ip6 = $ip6;
        $this->port = $port;
        $this->port6 = $port6;
    }

    public function getServer(): CoreServer {
        return $this->server;
    }

    public function getIp(): string {
        return $this->ip;
    }

    public function getIp6(): string {
        return $this->ip6;
    }

    public function getPort(): int {
        return $this->port;
    }

    public function getPort6(): int {
        return $this->port6;
    }

    public function start(){
        $this->packetHandler = new PacketHandler($this->getServer(), new ServerSocket($this->getIp(), $this->getPort(), 4));
    }

    public function getPacketHandler(): PacketHandler {
        return $this->packetHandler;
    }

}

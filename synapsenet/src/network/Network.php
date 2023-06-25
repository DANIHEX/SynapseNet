<?php

declare(strict_types=1);

namespace synapsenet\network;

use synapsenet\core\CoreServer;
use synapsenet\network\protocol\ServerSocket;
use synapsenet\network\protocol\packets\PacketHandler;

class Network {

    /** @var CoreServer */
    public CoreServer $server;

    /** @var int */
    public int $randId;

    /** @var string */
    protected string $ip;
    /** @var string */
    protected string $ip6;

    /** @var int */
    protected int $port;
    /** @var int */
    protected int $port6;

    /** @var PacketHandler */
    private PacketHandler $packetHandler;

    /**
     * @param CoreServer $server
     * @param string $ip
     * @param string $ip6
     * @param int $port
     * @param int $port6
     */
    public function __construct(CoreServer $server, string $ip, string $ip6, int $port, int $port6) {
        $this->server = $server;
        $this->randId = mt_rand(0, PHP_INT_MAX);
        $this->ip = $ip;
        $this->ip6 = $ip6;
        $this->port = $port;
        $this->port6 = $port6;
    }

    /**
     * @return CoreServer
     */
    public function getServer(): CoreServer {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getRandomId(): int {
        return $this->randId;
    }

    /**
     * @return string
     */
    public function getIp(): string {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getPort(): int {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getIp6(): string {
        return $this->ip6;
    }

    /**
     * @return int
     */
    public function getPort6(): int {
        return $this->port6;
    }

    /**
     * @return PacketHandler
     */
    public function getPacketHandler(): PacketHandler {
        return $this->packetHandler;
    }

    /**
     * @return void
     */
    public function start(): void {
        $this->packetHandler = new PacketHandler($this->getServer(), new ServerSocket($this->getIp(), $this->getPort(), 4), $this->getRandomId());
    }
}

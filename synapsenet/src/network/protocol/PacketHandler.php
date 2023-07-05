<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol;

use Exception;
use synapsenet\core\CoreServer;
use synapsenet\network\Address;
use synapsenet\network\ConnectionManager;
use synapsenet\network\protocol\raknet\Handshake;
use synapsenet\network\protocol\raknet\RaknetPacketHandler;

class PacketHandler {

    /**
     * @var CoreServer
     */
    public CoreServer $server;

    /**
     * @var ServerSocket
     */
    public ServerSocket $socket;

    /**
     * @var RaknetPacketHandler
     */
    public RaknetPacketHandler $raknet;

    /**
     * @var Handshake
     */
    public Handshake $handshake;

    /**
     * @param CoreServer $server
     * @param ServerSocket $socket
     */
    public function __construct(CoreServer $server, ServerSocket $socket) {
        $this->server = $server;
        $this->socket = $socket;
        $this->handshake = new Handshake($this);
    }

    /**
     * @return CoreServer
     */
    public function getServer(): CoreServer {
        return $this->server;
    }

    /**
     * @return ServerSocket
     */
    public function getSocket(): ServerSocket {
        return $this->socket;
    }

    /**
     * @return Handshake
     */
    public function getHandshake(): Handshake {
        return $this->handshake;
    }

    /**
     * @return ConnectionManager
     */
    public function getConnectionManager(): ConnectionManager {
        return $this->getServer()->getNetwork()->getConnectionManager();
    }

    /**
     * @param Address $address
     * @param Packet $packet
     * @return void
     */
    public function send(Address $address, Packet $packet): void {
        $this->getSocket()->write($packet->make(), $address->getIp(), $address->getPort());
    }

    /**
     * @param $address
     * @param $buffer
     * @return void
     */
    public function receive(&$address, &$buffer): void {
        $this->getSocket()->read($buffer, $source, $port);
        $address = new Address(4, $source, $port);
    }

    /**
     * @throws Exception
     */
    final public function process(): void {
        $this->receive($address, $buffer);

        if(is_null($buffer)) return;

        if($this->getConnectionManager()->isConnected($address)){
            $connection = $this->getConnectionManager()->getConnection($address);
            $connection->handle($buffer);
        } else {
            $this->getHandshake()->handle($address, $buffer);
        }
    }

}

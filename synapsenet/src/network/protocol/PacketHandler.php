<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol;

use Exception;
use synapsenet\core\CoreServer;
use synapsenet\network\Address;
use synapsenet\network\ConnectionManager;
use synapsenet\network\Network;
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
        return Network::getInstance()->getConnectionManager();
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
     * @param $buffer
     * @param $source
     * @param $port
     * @return void
     */
    public function receive(&$buffer, &$source, &$port): void {
        $this->getSocket()->read($buffer, $source, $port);
    }

    /**
     * @throws Exception
     */
    final public function process(): void {
        $this->receive($buffer, $source, $port);

        if(is_null($buffer)) return;

        $address = new Address(4, $source, $port);

        if($this->getConnectionManager()->isConnected($address)){
            $connection = $this->getConnectionManager()->getConnection($address);
            $connection->handle($buffer);
        } else {
            $this->getHandshake()->handle($address, $buffer);
        }
    }

}

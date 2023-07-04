<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol;

use Exception;
use synapsenet\core\CoreServer;
use synapsenet\network\Address;
use synapsenet\network\protocol\raknet\RaknetPacketHandler;

class PacketHandler {

    public CoreServer $server;

    public ServerSocket $socket;

    public RaknetPacketHandler $raknet;

    /**
     * @param CoreServer $server
     * @param ServerSocket $socket
     * @param int $randId
     */
    public function __construct(CoreServer $server, ServerSocket $socket) {
        $this->server = $server;
        $this->socket = $socket;
        // $this->raknet = new RaknetPacketHandler($server, $socket);
        // $this->bedrock = new BedrockPacketHandler();
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
     * @return RaknetPacketHandler
     */
    public function getRaknetHandler(): RaknetPacketHandler {
        return $this->raknet;
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

        switch(ord($buffer[0])){
            case 0xfe:
                $this->handleBedrock($address, $buffer);
                break;
            default:
                $this->handleRaknet($address, $buffer);
        }
    }

    /**
     * @param Address $address
     * @param string $buffer
     * @return void
     */
    public function handleRaknet(Address $address, string $buffer): void {
        if($this->getServer()->getNetwork()->getConnectionManager()->isConnected($address)){
            // Handle connected packets:

        } else {
            // Handle unconnected packets:

        }
    }

    public function handleBedrock(Address $address, string $buffer){
        
    }

}

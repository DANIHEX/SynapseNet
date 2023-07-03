<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol;

use Exception;
use synapsenet\core\CoreServer;
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
    public function __construct(CoreServer $server, ServerSocket $socket, int $randId) {
        $this->server = $server;
        $this->socket = $socket;
        $this->raknet = new RaknetPacketHandler($server, $socket, $randId);
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
     * @throws Exception
     */
    public function process(): void {
        $this->getSocket()->read($buffer, $source, $port);

        if(is_null($buffer)) return;

        switch(ord($buffer[0])){
            case 0xfe:
                // TODO: Bedrock Packet
                break;
            default:
                $this->getRaknetHandler()->handle($buffer, $source, $port);
        }
    }

}

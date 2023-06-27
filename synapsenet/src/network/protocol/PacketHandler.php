<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol;

use synapsenet\binary\Buffer;
use synapsenet\core\CoreServer;
use synapsenet\network\protocol\raknet\RaknetPacketHandler;

class PacketHandler {

    public CoreServer $server;

    public ServerSocket $socket;

    public RaknetPacketHandler $raknet;

    // public RaknetPacketHandler $bedrock;

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

    public function proccess(){
        $this->getSocket()->read($buf, $source, $port);

        if(is_null($buf)) return;

        $buffer = new Buffer($buf);
        $id = $buffer->get(1);
        switch($id){
            case 0xfe:
                // TODO: Bedrock Packet
                break;
            default:
                $this->getRaknetHandler()->handle($buf, $source, $port);
        }
    }

}

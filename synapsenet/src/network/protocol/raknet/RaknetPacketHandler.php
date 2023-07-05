<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet;

use Exception;
use synapsenet\core\CoreServer;
use synapsenet\network\Address;
use synapsenet\network\Connection;
use synapsenet\network\protocol\raknet\packets\ConnectedPing;
use synapsenet\network\protocol\raknet\packets\ConnectedPong;
use synapsenet\network\protocol\raknet\packets\ConnectionRequest;
use synapsenet\network\protocol\raknet\packets\ConnectionRequestAccepted;
use synapsenet\network\protocol\raknet\packets\FrameSetPacket;
use synapsenet\network\protocol\raknet\packets\OpenConnectionReply1;
use synapsenet\network\protocol\raknet\packets\OpenConnectionReply2;
use synapsenet\network\protocol\raknet\packets\OpenConnectionRequest1;
use synapsenet\network\protocol\raknet\packets\OpenConnectionRequest2;
use synapsenet\network\protocol\raknet\packets\UnconnectedPing;
use synapsenet\network\protocol\raknet\packets\UnconnectedPong;
use synapsenet\network\protocol\raknet\packets\UnknownPacket;
use synapsenet\network\protocol\ServerSocket;

class RaknetPacketHandler {

    public CoreServer $server;

    public ServerSocket $socket;

    public int $randId;

    public array $sessions = [];

    public function __construct(CoreServer $server, ServerSocket $socket, int $randId) {
        $this->server = $server;
        $this->socket = $socket;
        $this->randId = $randId;
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
     * @return int
     */
    public function getRandomId(): int {
        return $this->randId;
    }

    /**
     * @param RaknetPacket $packet
     * @param string $dest
     * @param int $port
     * @return void
     */
    public function sendPacket(RaknetPacket $packet, string $dest, int $port): void {
        CoreServer::getInstance()->getLogger()->info("Sending packet id(" . $packet->getPacketId() . ") to: " . $dest . ":" . $port);
        ServerSocket::getInstance()->write($packet->make(), $dest, $port);
    }


    /**
     * @param string $buffer
     * @param string $source
     * @param int $port
     * @return void
     * @throws Exception
     */
    public function handle(string $buffer, string $source, int $port): void {
        $packet = RaknetPacketMap::match($buffer);

        if($packet instanceof UnknownPacket){
            CoreServer::getInstance()->getLogger()->info("Unknown packet with id(" . $packet->getPacketId() . ") received. Source: " . $source . ":" . $port);
            return;
        }

        CoreServer::getInstance()->getLogger()->info("Packet with id(" . $packet->getPacketId() . ") received. Source: " . $source . ":" . $port);

        switch($packet->getPacketId()) {
//            case RaknetPacketIds::CONNECTED_PING:
//                /** @var ConnectedPing $packet */
//                $this->handleConnectedPing($packet, $source, $port);
//                break;

//            case RaknetPacketIds::CONNECTION_REQUEST:
//                /** @var ConnectionRequest $packet */
//                $this->handleConnectionRequest($packet, $source, $port);
//                break;
//            case RaknetPacketIds::NEW_INCOMING_CONNECTION:
//
//                break;
        }
    }

//    /**
//     * @param ConnectedPing $packet
//     * @param string $source
//     * @param int $port
//     * @return void
//     */
//    public function handleConnectedPing(ConnectedPing $packet, string $source, int $port): void {
//        $pk = new ConnectedPong();
//        $pk->pingTime = $packet->getTime();
//        $pk->pongTime = time();
//        $this->sendPacket($pk, $source, $port);
//    }
//
//    /**
//     * @param ConnectionRequest $packet
//     * @param string $source
//     * @param int $port
//     * @return void
//     */
//    public function handleConnectionRequest(ConnectionRequest $packet, string $source, int $port): void {
//        $guid = $packet->getGuid();
//        $pk = new ConnectionRequestAccepted();
//        $pk->clientAddress = $this->sessions[$guid];
//        $pk->requestTime = $packet->getTime();
//        $pk->time = time();
//        $this->sendPacket($pk, $source, $port);
//    }
}

<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet;

use synapsenet\core\CoreServer;
use synapsenet\network\Address;
use synapsenet\network\protocol\raknet\packets\ConnectedPong;
use synapsenet\network\protocol\raknet\packets\ConnectionRequestAccepted;
use synapsenet\network\protocol\raknet\packets\OpenConnectionReply1;
use synapsenet\network\protocol\raknet\packets\OpenConnectionReply2;
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
     *
     * @return void
     */
    public function sendPacket(RaknetPacket $packet, string $dest, int $port): void {
        CoreServer::getInstance()->getLogger()->info("Sending packet id(" . $packet->getPacketId() . ") to: " . $dest . ":" . $port);
        ServerSocket::getInstance()->write($packet->make(), $dest, $port);
    }

    /**
     * @param RaknetPacket $packet
     * @param string $source
     * @param int $port
     *
     * @return void
     */
    public function handle(string $buffer, string $source, int $port): void {
        $packet = RaknetPacketMap::match($buffer);
        if($packet instanceof UnknownPacket) {
            CoreServer::getInstance()->getLogger()->info("Unknown packet with id(" . $packet->getPacketId() . ") received. Source: " . $source . ":" . $port);
            return;
        }

        CoreServer::getInstance()->getLogger()->info("Packet with id(" . $packet->getPacketId() . ") received. Source: " . $source . ":" . $port);

        switch($packet->getPacketId()) {
            case RaknetPacketIds::CONNECTED_PING:
                /** @var ConnectedPing $packet */
                $pk = new ConnectedPong();
                $pk->pingTime = $packet->getTime();
                $pk->pongTime = time();
                $this->sendPacket($pk, $source, $port);
                break;
            case RaknetPacketIds::UNCONNECTED_PING:
            case RaknetPacketIds::UNCONNECTED_PING_OPEN:
                /** @var UnconnectedPing $packet */
                $pk = new UnconnectedPong();
                $pk->time = $packet->getTime();
                $pk->serverGuid = $this->getRandomId();
                $pk->magic = $packet->getMagic();
                $pk->serverIdString = $this->server->getQuery()->getQueryString();
                $this->sendPacket($pk, $source, $port);
                break;
            case RaknetPacketIds::OPEN_CONNECTION_REQUEST_1:
                /** @var OpenConnectionRequest1 $packet */
                $pk = new OpenConnectionReply1();
                $pk->magic = $packet->getMagic();
                $pk->serverGuid = $this->getRandomId();
                $pk->useSecurity = false;
                $pk->mtuSize = $packet->getMtuSize();
                $this->sendPacket($pk, $source, $port);
                break;
            case RaknetPacketIds::OPEN_CONNECTION_REQUEST_2:
                /** @var OpenConnectionRequest2 $packet */
                $pk = new OpenConnectionReply2();
                $pk->magic = $packet->getMagic();
                $pk->serverGuid = $this->getRandomId();
                $pk->clientAddress = new Address(4, $source, $port);
                $pk->mtuSize = $packet->getMtuSize();
                $pk->encryptionEnabled = false;
                $this->sendPacket($pk, $source, $port);
                break;
            case RaknetPacketIds::CONNECTION_REQUEST:
                /** @var ConnectionRequest $packet */
                $guid = $packet->getGuid();
                $pk = new ConnectionRequestAccepted();
                $pk->clientAddress = $this->sessions[$guid];
                $pk->requestTime = $packet->getTime();
                $pk->time = time();
                break;
            case RaknetPacketIds::NEW_INCOMING_CONNECTION:

                break;
            case RaknetPacketIds::NACK:

                break;
            case RaknetPacketIds::ACK:

                break;
        }
    }
}

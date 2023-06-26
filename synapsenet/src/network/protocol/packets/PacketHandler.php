<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets;

use Exception;
use synapsenet\core\CoreServer;
use synapsenet\network\Address;
use synapsenet\network\protocol\packets\handshake\ConnectedPong;
use synapsenet\network\protocol\packets\handshake\ConnectedPing;
use synapsenet\network\protocol\packets\handshake\OpenConnectionReply1;
use synapsenet\network\protocol\packets\handshake\OpenConnectionReply2;
use synapsenet\network\protocol\ServerSocket;
use synapsenet\network\protocol\packets\handshake\UnconnectedPong;
use synapsenet\network\protocol\packets\handshake\UnconnectedPing;
use synapsenet\network\protocol\packets\handshake\UnknownPacket;
use synapsenet\network\protocol\packets\handshake\OpenConnectionRequest1;
use synapsenet\network\protocol\packets\handshake\OpenConnectionRequest2;

class PacketHandler {

    public CoreServer $server;

    public ServerSocket $socket;

    public int $randId;

    /**
     * @param CoreServer $server
     * @param ServerSocket $socket
     * @param int $randId
     */
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
     * @param Packet $packet
     * @param string $dest
     * @param int $port
     *
     * @return void
     */
    public function sendPacket(Packet $packet, string $dest, int $port): void {
        $this->getServer()->getLogger()->info("Sending packet id(" . $packet->getPacketId() . ") to: " . $dest . ":" . $port);
        $this->getSocket()->write($packet->make(), $dest, $port);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function proccess(): void {
        $this->socket->read($buffer, $source, $port);
        if(is_null($buffer)) {
            return;
        }

        $packet = PacketMap::match($buffer);
        if($packet instanceof UnknownPacket) {
            $this->getServer()->getLogger()->info("Unknown packet with id(" . $packet->getPacketId() . ") received. Source: " . $source . ":" . $port);
            return;
        }
        $this->getServer()->getLogger()->info("Packet with id(" . $packet->getPacketId() . ") received. Source: " . $source . ":" . $port);

        $this->handle($packet, $source, $port);
    }

    /**
     * @param Packet $packet
     * @param string $source
     * @param int $port
     *
     * @return void
     */
    public function handle(Packet $packet, string $source, int $port): void {
        switch($packet->getPacketId()) {
            case PacketIdentifiers::CONNECTED_PING:
                /** @var ConnectedPing $packet */
                $pk = new ConnectedPong();
                $pk->pingTime = $packet->getTime();
                $pk->pongTime = time();
                $this->sendPacket($pk, $source, $port);
                break;
            case PacketIdentifiers::UNCONNECTED_PING:
            case PacketIdentifiers::UNCONNECTED_PING_OPEN:
                /** @var UnconnectedPing $packet */
                $pk = new UnconnectedPong();
                $pk->time = $packet->getTime();
                $pk->serverGuid = $this->getRandomId();
                $pk->magic = $packet->getMagic();
                $pk->serverIdString = $this->server->getQuery()->getQueryString();
                $this->sendPacket($pk, $source, $port);
                break;
            case PacketIdentifiers::OPEN_CONNECTION_REQUEST_1:
                /** @var OpenConnectionRequest1 $packet */
                $pk = new OpenConnectionReply1();
                $pk->magic = $packet->getMagic();
                $pk->serverGuid = $this->getRandomId();
                $pk->useSecurity = false;
                $pk->mtuSize = $packet->getMtuSize();
                $this->sendPacket($pk, $source, $port);
                break;
            case PacketIdentifiers::OPEN_CONNECTION_REQUEST_2:
                /** @var OpenConnectionRequest2 $packet */
                $pk = new OpenConnectionReply2();
                $pk->magic = $packet->getMagic();
                $pk->serverGuid = $this->getRandomId();
                $pk->clientAddress = new Address(4, $source, $port);
                $pk->mtuSize = $packet->getMtuSize();
                $pk->encryptionEnabled = false;
                $this->sendPacket($pk, $source, $port);
                break;
            case PacketIdentifiers::CONNECTION_REQUEST:
                $pk = new ConnectionRequestAccepted();
                break;
        }
    }
}

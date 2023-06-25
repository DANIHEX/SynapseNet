<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets;

use Exception;
use synapsenet\binary\Buffer;
use synapsenet\core\CoreServer;
use synapsenet\network\protocol\ServerSocket;
use synapsenet\network\protocol\packets\handshake\UnconnectedPong;
use synapsenet\network\protocol\packets\handshake\UnconnectedPing;

class PacketHandler {

    public CoreServer $server;

    public ServerSocket $socket;

    public int $randId;

    /**
     * @param CoreServer $server
     * @param ServerSocket $socket
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
     * @param PacketSendInterface $packet
     * @param string $dest
     * @param int $port
     *
     * @return void
     */
    public function sendPacket(PacketSendInterface $packet, string $dest, int $port): void {
        $this->getServer()->getLogger()->info("Sending an UnconnectedPong packet to: " . $dest . ":" . $port);
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

        $binary = new Buffer($buffer);
        $id = ord($binary->get(1));
        switch($id) {
            case 0x01:
            case 0x02:
                $this->getServer()->getLogger()->info("Recieved an UnconnectedPing packet from: " . $source . ":" . $port);
                $this->handle(new UnconnectedPing($buffer), $source, $port);
                break;
        }
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
            case 0x01:
            case 0x02:
                $pk = new UnconnectedPong();
                $pk->time = $packet->getTime();
                $pk->serverGuid = $this->getRandomId();
                $pk->serverIdString = $this->server->getQuery()->getQueryString();
                $this->sendPacket($pk, $source, $port);
                break;
        }
    }
}

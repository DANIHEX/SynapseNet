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

    /**
     * @param CoreServer $server
     * @param ServerSocket $socket
     */
    public function __construct(CoreServer $server, ServerSocket $socket) {
        $this->server = $server;
        $this->socket = $socket;
    }

    /**
     * @return CoreServer
     */
    public function getServer(): CoreServer {
        return $this->server;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function proccess(): void {
        $this->socket->read($buffer, $source, $port);
        var_dump($buffer);
        if(is_null($buffer)) {
            return;
        }

        $binary = new Buffer($buffer);
        $id = $binary->get(1);
        switch($id) {
            case 0x01:
            case 0x02:
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
                $packet = new UnconnectedPong();
                $packet->time = microtime(true);
                $packet->serverGuid = 612126584945;
                $packet->serverIdString = $this->server->getQuery()->getQueryString();
                $this->sendPacket($packet, $source, $port);
                break;
        }
    }

    /**
     * @param PacketSendInterface $packet
     * @param string $dest
     * @param int $port
     *
     * @return void
     */
    public function sendPacket(PacketSendInterface $packet, string $dest, int $port): void {
        $this->socket->write($packet->make(), $dest, $port);
    }
}

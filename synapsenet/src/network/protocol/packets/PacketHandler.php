<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol\packets;

use synapsenet\binary\Buffer;
use synapsenet\core\CoreServer;
use synapsenet\network\protocol\packets\handshake\PacketSendInterface;
use synapsenet\network\protocol\packets\handshake\UnconnectedPing;
use synapsenet\network\protocol\packets\handshake\UnconnectedPong;
use synapsenet\network\protocol\packets\Packet;
use synapsenet\network\protocol\ServerSocket;

class PacketHandler {

    public CoreServer $server; 
    public ServerSocket $socket; 

    public function __construct(CoreServer $server, ServerSocket $socket){
        $this->server = $server;
        $this->socket = $socket;
    }

    public function getServer(): CoreServer {
        return $this->server;
    }

    public function sendPacket(PacketSendInterface $packet, string $dest, int $port){
        $this->socket->write($packet->make(), $dest, $port);
    }

    public function proccess(){
        $this->socket->read($buffer, $source, $port);
        var_dump($buffer);
        if(is_null($buffer)) return;
        $binary = new Buffer($buffer);
        $id = $binary->get(1);
        switch($id){
            case 0x01:
            case 0x02:
                $this->handle(new UnconnectedPing($buffer), $source, $port);
                break;
        }
    }

    public function handle(Packet $packet, string $source, int $port){
        switch($packet->getPacketId()){
            case 0x01:
            case 0x02:
                $packet = new UnconnectedPong();
                $packet->time = microtime(true);
                $packet->serverGuid = 612126584945;
                $packet->serverIdString = $this->server->getQuery();
                $this->sendPacket($packet, $source, $port);
                break;
        }
    }

}

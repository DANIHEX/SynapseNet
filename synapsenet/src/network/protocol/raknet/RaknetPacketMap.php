<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet;

use Exception;
use synapsenet\network\protocol\Packet;
use synapsenet\network\protocol\raknet\packets\ConnectedPing;
use synapsenet\network\protocol\raknet\packets\ConnectedPong;
use synapsenet\network\protocol\raknet\packets\ConnectionRequest;
use synapsenet\network\protocol\raknet\packets\ConnectionRequestAccepted;
use synapsenet\network\protocol\raknet\packets\NewIncomingConnection;
use synapsenet\network\protocol\raknet\packets\OpenConnectionReply1;
use synapsenet\network\protocol\raknet\packets\OpenConnectionReply2;
use synapsenet\network\protocol\raknet\packets\OpenConnectionRequest1;
use synapsenet\network\protocol\raknet\packets\OpenConnectionRequest2;
use synapsenet\network\protocol\raknet\packets\UnconnectedPing;
use synapsenet\network\protocol\raknet\packets\UnconnectedPong;
use synapsenet\network\protocol\raknet\packets\UnknownPacket;

class RaknetPacketMap {

    /**
     * @param string $buf
     *
     * @return Packet
     * @throws Exception
     */
    public static function match(string $buf): Packet {
        return match (ord($buf[0])) {
            RaknetPacketIds::CONNECTED_PING => new ConnectedPing($buf),
            RaknetPacketIds::UNCONNECTED_PING, RaknetPacketIds::UNCONNECTED_PING_OPEN => new UnconnectedPing($buf),
            RaknetPacketIds::CONNECTED_PONG => new ConnectedPong(),
            RaknetPacketIds::OPEN_CONNECTION_REQUEST_1 => new OpenConnectionRequest1($buf),
            RaknetPacketIds::OPEN_CONNECTION_REPLY_1 => new OpenConnectionReply1(),
            RaknetPacketIds::OPEN_CONNECTION_REQUEST_2 => new OpenConnectionRequest2($buf),
            RaknetPacketIds::OPEN_CONNECTION_REPLY_2 => new OpenConnectionReply2(),
            RaknetPacketIds::CONNECTION_REQUEST => new ConnectionRequest($buf),
            RaknetPacketIds::CONNECTION_REQUEST_ACCEPTED => new ConnectionRequestAccepted(),
            RaknetPacketIds::NEW_INCOMING_CONNECTION => new NewIncomingConnection($buf),
            // RaknetPacketIds::DISCONNECT => new Disconnect(),
            // RaknetPacketIds::INCOMPATIBLE_PROTOCOL => new IncompatibleProtocol(),
            RaknetPacketIds::UNCONNECTED_PONG => new UnconnectedPong(),
            default => new UnknownPacket($buf)
        };
    }

}

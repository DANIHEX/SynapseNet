<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets;

use synapsenet\binary\Buffer;
use synapsenet\network\protocol\packets\handshake\ConnectedPing;
use synapsenet\network\protocol\packets\handshake\ConnectedPong;
use synapsenet\network\protocol\packets\handshake\OpenConnectionReply1;
use synapsenet\network\protocol\packets\handshake\OpenConnectionRequest1;
use synapsenet\network\protocol\packets\handshake\OpenConnectionReply2;
use synapsenet\network\protocol\packets\handshake\OpenConnectionRequest2;
use synapsenet\network\protocol\packets\handshake\UnconnectedPing;
use synapsenet\network\protocol\packets\handshake\UnconnectedPong;
use synapsenet\network\protocol\packets\handshake\UnknownPacket;

class PacketMap {

    /**
     * @param string $buf
     *
     * @return Packet
     */
    public static function match(string $buf): Packet {
        $buffer = new Buffer($buf);
        return match (ord($buffer->get(1))) {
            PacketIdentifiers::CONNECTED_PING => new ConnectedPing($buf),
            PacketIdentifiers::UNCONNECTED_PING, PacketIdentifiers::UNCONNECTED_PING_OPEN => new UnconnectedPing($buf),
            PacketIdentifiers::CONNECTED_PONG => new ConnectedPong(),
            PacketIdentifiers::OPEN_CONNECTION_REQUEST_1 => new OpenConnectionRequest1($buf),
            PacketIdentifiers::OPEN_CONNECTION_REPLY_1 => new OpenConnectionReply1(),
            PacketIdentifiers::OPEN_CONNECTION_REQUEST_2 => new OpenConnectionRequest2($buf),
            PacketIdentifiers::OPEN_CONNECTION_REPLY_2 => new OpenConnectionReply2(),
            PacketIdentifiers::UNCONNECTED_PONG => new UnconnectedPong(),
            default => new UnknownPacket($buf)
        };
    }

}

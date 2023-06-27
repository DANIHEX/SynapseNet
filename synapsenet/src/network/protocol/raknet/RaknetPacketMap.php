<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet;

use synapsenet\binary\Buffer;
use synapsenet\network\protocol\raknet\packets\ConnectedPing;
use synapsenet\network\protocol\raknet\packets\ConnectionRequest;
use synapsenet\network\protocol\raknet\packets\ConnectionRequestAccepted;
use synapsenet\network\protocol\raknet\packets\FrameSetPacket;
use synapsenet\network\protocol\raknet\packets\NewIncomingAddress;
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
     * @return RaknetPacket
     */
    public static function match(string $buf): RaknetPacket {
        $buffer = new Buffer($buf);
        return match (ord($buffer->get(1))) {
            RaknetPacketIds::CONNECTED_PING => new ConnectedPing($buf),
            RaknetPacketIds::UNCONNECTED_PING, RaknetPacketIds::UNCONNECTED_PING_OPEN => new UnconnectedPing($buf),
            RaknetPacketIds::CONNECTED_PONG => new ConnectedPing($buf),
            RaknetPacketIds::OPEN_CONNECTION_REQUEST_1 => new OpenConnectionRequest1($buf),
            RaknetPacketIds::OPEN_CONNECTION_REPLY_1 => new OpenConnectionReply1(),
            RaknetPacketIds::OPEN_CONNECTION_REQUEST_2 => new OpenConnectionRequest2($buf),
            RaknetPacketIds::OPEN_CONNECTION_REPLY_2 => new OpenConnectionReply2(),
            RaknetPacketIds::CONNECTION_REQUEST => new ConnectionRequest($buf),
            RaknetPacketIds::CONNECTION_REQUEST_ACCEPTED => new ConnectionRequestAccepted(),
            RaknetPacketIds::NEW_INCOMING_CONNECTION => new NewIncomingAddress($buf),
            // RaknetPacketIds::DISCONNECT => new Disconnect(),
            // RaknetPacketIds::INCOMPATIBLE_PROTOCOL => new IncompatibleProtocol(),
            RaknetPacketIds::UNCONNECTED_PONG => new UnconnectedPong(),
            RaknetPacketIds::FRAME_SET_PACKET_0 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_0, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_1 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_1, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_2 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_2, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_3 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_3, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_4 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_4, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_5 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_5, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_6 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_6, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_7 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_7, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_8 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_8, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_9 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_9, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_10 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_10, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_11 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_11, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_12 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_12, $buf),
            RaknetPacketIds::FRAME_SET_PACKET_13 => new FrameSetPacket(RaknetPacketIds::FRAME_SET_PACKET_13, $buf),
            // RaknetPacketIds::NACK => new NACK($buf),
            // RaknetPacketIds::ACK => new ACK($buf),
            default => new UnknownPacket($buf)
        };
    }

}

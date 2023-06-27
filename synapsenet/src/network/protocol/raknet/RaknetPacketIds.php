<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet;

class RaknetPacketIds {

    /**
     * Handshake packets:
     * 
     * @const int
     */
    public const CONNECTED_PING = 0x00;
    public const UNCONNECTED_PING = 0x01;
    public const UNCONNECTED_PING_OPEN = 0x02;
    public const CONNECTED_PONG = 0x03;
    public const OPEN_CONNECTION_REQUEST_1 = 0x05;
    public const OPEN_CONNECTION_REPLY_1 = 0x06;
    public const OPEN_CONNECTION_REQUEST_2 = 0x07;
    public const OPEN_CONNECTION_REPLY_2 = 0x08;
    public const CONNECTION_REQUEST = 0x09;
    public const CONNECTION_REQUEST_ACCEPTED = 0x10;
    public const NEW_INCOMING_CONNECTION = 0x13;
    public const DISCONNECT = 0x15;
    public const INCOMPATIBLE_PROTOCOL = 0x19;
    public const UNCONNECTED_PONG = 0x1c;
    public const FRAME_SET_PACKET_0 = 0x80;
    public const FRAME_SET_PACKET_1 = 0x81;
    public const FRAME_SET_PACKET_2 = 0x82;
    public const FRAME_SET_PACKET_3 = 0x83;
    public const FRAME_SET_PACKET_4 = 0x84;
    public const FRAME_SET_PACKET_5 = 0x85;
    public const FRAME_SET_PACKET_6 = 0x86;
    public const FRAME_SET_PACKET_7 = 0x87;
    public const FRAME_SET_PACKET_8 = 0x88;
    public const FRAME_SET_PACKET_9 = 0x89;
    public const FRAME_SET_PACKET_10 = 0x8a;
    public const FRAME_SET_PACKET_11 = 0x8b;
    public const FRAME_SET_PACKET_12 = 0x8c;
    public const FRAME_SET_PACKET_13 = 0x8d;
    public const NACK = 0xa0;
    public const ACK = 0xc0;
    public const GAME_PACKET = 0xfe;

}

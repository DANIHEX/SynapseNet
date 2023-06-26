<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets;

class PacketIdentifiers {

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
    public const UNCONNECTED_PONG = 0x1c;

}

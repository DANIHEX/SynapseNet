<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol\packets;

class PacketIdentifiers {

    /**
     * Handshake packets:
     * 
     * @const int
     */
    const CONNECTED_PING = 0x00;
    const UNCONNECTED_PING = 0x01;
    const UNCONNECTED_PING_OPEN = 0x02;
    const CONNECTED_PONG = 0x03;
    const OPEN_CONNECTION_REQUEST_1 = 0x05;
    const OPEN_CONNECTION_REPLY_1 = 0x06;
    const OPEN_CONNECTION_REQUEST_2 = 0x07;
    const OPEN_CONNECTION_REPLY_2 = 0x08;
    const CONNECTION_REQUEST = 0x09;
    const UNCONNECTED_PONG = 0x1c;

}

<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets;

interface PacketRead {

    /**
     * @return Packet
     */
    public function extract(): Packet;
}

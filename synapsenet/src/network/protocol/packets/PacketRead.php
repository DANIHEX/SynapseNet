<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol\packets;

interface PacketRead {

    public function extract(): Packet;

}

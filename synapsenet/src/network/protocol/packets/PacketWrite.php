<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol\packets;

interface PacketWrite {

    public function make(): string;

}

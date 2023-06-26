<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets;

interface PacketWrite {

    /**
     * @return string
     */
    public function make(): string;
}

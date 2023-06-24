<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\packets;

interface PacketReciveInterface {

    public function getDataArray();

    public function extract();

}

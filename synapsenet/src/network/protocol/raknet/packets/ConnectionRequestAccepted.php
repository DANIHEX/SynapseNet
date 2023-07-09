<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use synapsenet\binary\Binary;
use synapsenet\network\Address;
use synapsenet\network\protocol\Packet;
use synapsenet\network\protocol\raknet\RaknetPacketIds;

class ConnectionRequestAccepted extends Packet {

    /** @var int */
    private int $packetId = RaknetPacketIds::CONNECTION_REQUEST_ACCEPTED;

    /** @var Address */
    public Address $clientAddress;

    /** 
     * According to the wiki 10 same addresses will work fine
     * 
     * @var array
     */
    public array $internalIds;

    /** @var int */
    public int $requestTime;

    /** @var int */
    public int $time;

    public function __construct() {
        parent::__construct($this->packetId, "");
    }

    public function extract(): ConnectionRequestAccepted {
        return $this;
    }

    /**
     * @return string
     */
    public function make(): string {
        $ids = "";
        foreach($this->internalIds as $id){
            $ids .= $this->getAddressBuffer($id);
        }
        $this->buffer .= chr($this->getPacketId());
        $this->buffer .= $this->getAddressBuffer($this->clientAddress);
        $this->buffer .= $ids;
        $this->buffer .= Binary::writeLong($this->requestTime);
        $this->buffer .= Binary::writeLong($this->time);

        return $this->buffer;
    }
}

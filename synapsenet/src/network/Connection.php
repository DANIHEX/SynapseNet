<?php

namespace synapsenet\network;

use Exception;
use synapsenet\binary\Buffer;
use synapsenet\network\protocol\raknet\packets\FrameSetPacket;

class Connection {

    /**
     * @var Address
     */
    public Address $address;
    /**
     * @var int
     */
    private int $protocol;
    /**
     * @var int
     */
    public int $guid;

    /**
     * @param Address $address
     * @param int $protocol
     * @param int $guid
     */
    public function __construct(Address $address, int $protocol, int $guid){
        $this->address = $address;
        $this->protocol = $protocol;
        $this->guid = $guid;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address {
        return $this->address;
    }

    /**
     * @return int
     */
    public function getProtocol(): int {
        return $this->protocol;
    }

    /**
     * @return int
     */
    public function getGuid(): int {
        return $this->guid;
    }

    /**
     * RaknetPacket
     * Up / Send
     *
     * @return void
     */
    public function sendPacket(){
        
    }

    /**
     * RaknetPacket
     * Down / Receive
     *
     * @param string $buffer
     * @return void
     * @throws Exception
     */
    public function handle(string $buffer): void {
        $pid = ord($buffer[0]);
        if($pid >= ord(0x80) and $pid <= ord(0x8d)){
            $packet = new FrameSetPacket($pid, $buffer);
            $this->handleFrameSet($packet);
        }
    }

    /**
     * @param FrameSetPacket $packet
     * @return void
     */
    public function handleFrameSet(FrameSetPacket $packet): void {
        echo $packet->getBody();
    }

    public function disconnect(){
        
    }

}
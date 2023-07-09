<?php

namespace synapsenet\network;

use Exception;
use synapsenet\binary\Buffer;
use synapsenet\core\CoreServer;
use synapsenet\network\protocol\Packet;
use synapsenet\network\protocol\raknet\packets\ACK;
use synapsenet\network\protocol\raknet\packets\ConnectionRequest;
use synapsenet\network\protocol\raknet\packets\ConnectionRequestAccepted;
use synapsenet\network\protocol\raknet\packets\FrameSetPacket;
use synapsenet\network\protocol\raknet\RaknetPacketIds;
use synapsenet\network\protocol\raknet\RaknetPacketMap;
use synapsenet\network\protocol\ServerSocket;

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
     * @var array
     */
    public array $sendQueue = [];

    /**
     * FrameSetPacket by sequence number
     *
     * @var array
     */
    public array $sequences = [];

    public array $receiveOrder = [];

    public array $sendOrder = [];

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
     * @param Packet $packet
     * @param int $sequenceNumber
     * @param int $fragmentsCount
     * @return void
     * @throws Exception
     */
    public function addToQueue(Packet $packet, int $sequenceNumber = 0, int $fragmentsCount = 0): void {
        $buffer = $packet->make();
        regenerateSN:
        $sequenceNumber = mt_rand(0, (PHP_INT_MAX - 100000)) + 1;
        $exists = false;
        foreach($this->sendQueue as $queue){
            if($queue["sequenceNumber"] === $sequenceNumber){
                $exists = true;
                break;
            }
        }
        if($exists) goto regenerateSN;
        $time = intval(round(microtime(true), 3) * 1000);

        if(strlen($buffer) > Network::getInstance()->getMtuSize()){
            $fragmentSize = Network::getInstance()->getFragmentationSize();
            // TODO
        } else {
            $pk = new FrameSetPacket(0x80, $packet->make());
            $pk->sequenceNumber = 0;
            $pk->flags = 0b10000000;
            $pk->reliableFrameIndex = 0;
            $this->sendQueue[$time] = [
                "sequenceNumber" => $pk->sequenceNumber,
                "packet" => $pk
            ];
        }
    }

    /**
     * @param int $sequenceNumber
     * @return void
     */
    public function removeFromQueue(int $sequenceNumber): void {
        foreach($this->sendQueue as $time => $queue){
            if($queue["sequenceNumber"] === $sequenceNumber){
                unset($this->sendQueue[$time]);
                break;
            }
        }
    }

    /**
     * RaknetPacket
     * Up / Send
     *
     * @param Packet $packet
     * @return void
     * @throws Exception
     */
    public function sendPacket(Packet $packet): void {
        foreach($this->sendQueue as $sequenceNumber => $pk){
            CoreServer::getInstance()->getLogger()->info("Packet sent: 0x" . dechex($packet->getPacketId()));
        }
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
        CoreServer::getInstance()->getLogger()->info("Connected packet received: 0x" . dechex($pid));
        if($pid >= 0x80 and $pid <= 0x8d){
            $packet = new FrameSetPacket($pid, $buffer);
            $packet->extract();
            $this->handleFrameSet($packet);
        }
        if($pid === 0xc0 or $pid === 0xa0){
            $packet = new ACK($pid, $buffer);
            $packet->extract();
            $this->receiveReliability($packet);
        }
        $this->sendPackets();
    }

    /**
     * @param FrameSetPacket $packet
     * @return void
     * @throws Exception
     */
    public function handlePacket(Packet $packet): void {
        switch($packet->getPacketId()){
            case 0x09:
                /** @var ConnectionRequest $packet */
                $pk = new ConnectionRequestAccepted();
                $pk->clientAddress = $this->getAddress();
                $pk->internalIds = [
                    new Address(4, CoreServer::getInstance()->getIp(), CoreServer::getInstance()->getPort()),
                    new Address(4, CoreServer::getInstance()->getIp(), CoreServer::getInstance()->getPort()),
                    new Address(4, CoreServer::getInstance()->getIp(), CoreServer::getInstance()->getPort()),
                    new Address(4, CoreServer::getInstance()->getIp(), CoreServer::getInstance()->getPort()),
                    new Address(4, CoreServer::getInstance()->getIp(), CoreServer::getInstance()->getPort()),
                    new Address(4, CoreServer::getInstance()->getIp(), CoreServer::getInstance()->getPort()),
                    new Address(4, CoreServer::getInstance()->getIp(), CoreServer::getInstance()->getPort()),
                    new Address(4, CoreServer::getInstance()->getIp(), CoreServer::getInstance()->getPort()),
                    new Address(4, CoreServer::getInstance()->getIp(), CoreServer::getInstance()->getPort()),
                    new Address(4, CoreServer::getInstance()->getIp(), CoreServer::getInstance()->getPort())
                ];
                $pk->requestTime = $packet->getTime();
                $pk->time = time();
                $this->addToQueue($pk);
                break;
            default:

                break;
        }
    }

    /**
     * @param FrameSetPacket $packet
     * @return void
     * @throws Exception
     */
    public function handleFrameSet(FrameSetPacket $packet): void {
        CoreServer::getInstance()->getLogger()->info("Frame packet received: " . dechex(ord($packet->getBody()[0])));
//        $data = [
//            "sequenceNumber" => $packet->getSequenceNumber(),
//            "flags" => decbin(($packet->getFlags() | 0b00000000) >> 3),
//            "reliable" => $packet->isReliable(),
//            "ordered" => $packet->isOrdered(),
//            "sequenced" => $packet->isSequenced(),
//            "fragmented" => $packet->isFragmented(),
//            "length" => $packet->getLength(),
//            "body" => $packet->getBody()
//        ];
//        var_dump($data);

        $this->sequences[$packet->getSequenceNumber()] = $packet;

        if($packet->isFragmented()){
             $this->handleFragmented($packet);
        } else {
            if($this->sendReliability($packet)){
                $this->handlePacket(RaknetPacketMap::match($packet->getBody()));
            }
        }
    }

    /**
     * @param FrameSetPacket $packet
     * @return void
     */
    public function handleFragmented(FrameSetPacket $packet){
        // TODO
    }

    /**
     * @param FrameSetPacket $packet
     * @return bool
     * @throws Exception
     */
    public function sendReliability(FrameSetPacket $packet): bool {
        $reliable = false;
        $record = [
            "sequenceNumber" => $packet->getSequenceNumber()
        ];
        if($packet->isReliable()){
            $pk = new ACK(RaknetPacketIds::ACK);
            $reliable = true;
        } else {
            $pk = new ACK(RaknetPacketIds::NACK);
        }
        $pk->addRecord($record);
        $buffer = $pk->make();
        ServerSocket::getInstance()->write($buffer, $this->getAddress()->getIp(), $this->getAddress()->getPort());
        $this->sendPacket($pk);
        return $reliable;
    }

    /**
     * @param ACK $packet
     * @return void
     */
    public function receiveReliability(ACK $packet): void {
        foreach($packet->getRecords() as $record){
            if(count($record) === 1){
                foreach($this->sendQueue as $queue){
                    if($queue["sequenceNumber"] === $record["sequenceNumber"]){
                        $this->removeFromQueue($record["sequenceNumber"]);
                    }
                }
            } elseif(count($record) > 1) {
                $start = $record["startSequenceNumber"];
                $end = $record["endSequenceNumber"];
                foreach($this->sendQueue as $queue){
                    for($i = $start; $i === $end; $i++){
                        if($queue["sequenceNumber"] === $i){
                            $this->removeFromQueue($i);
                        }
                    }
                }
            }
        }
    }

    public function disconnect(){
        
    }

}
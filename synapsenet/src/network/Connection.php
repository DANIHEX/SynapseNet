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
     * Add a packet to send queue and make it fragmented if needed
     *
     * @param Packet $packet
     * @return void
     * @throws Exception
     */
    public function addToQueue(Packet $packet): void {
        $buffer = $packet->make();
        $length = strlen($buffer);

        // Make the packet fragmented
        if($length > Network::getInstance()->getMtuSize()){
            $fragmentSize = Network::getInstance()->getFragmentationSize();
            $count = intval($length / $fragmentSize);
            $remainder = $length % $fragmentSize;

            // Get a valid sequence of numbers
            rearrange:
            $rangeStart = mt_rand(0, (PHP_INT_MAX - 100000));
            $rangeEnd = $rangeStart + $count + 1;
            $rangedSequenceNumber = range($rangeStart, $rangeEnd);
            $rearrange = false;
            foreach($rangedSequenceNumber as $seqNum){
                foreach($this->sendQueue as $p){
                    if($p["sequenceNumber"] === $seqNum){
                        $rearrange = true;
                        break;
                    }
                }
            }
            unset($rangedSequenceNumber[array_key_last($rangedSequenceNumber)]);
            if($rearrange) goto rearrange;

            // Add fragmented packets to queue
            $offset = 0;
            $compoundSize = $remainder > 0 ? $count + 1 : $count;
            $compoundId = mt_rand(0, 0xfff);
            $index = 0;
            foreach($rangedSequenceNumber as $seqNum){
                $time = intval(round(microtime(true), 8) * 100000000);
                $pk = new FrameSetPacket(0x80, substr($buffer, $offset, $fragmentSize));
                $pk->sequenceNumber = $seqNum;
                $pk->flags = 0b10010000;
                $pk->length = strlen($buffer) << 3;
                $pk->reliableFrameIndex = $seqNum;
                $pk->fragmentCompoundSize = $compoundSize;
                $pk->fragmentCompoundId = $compoundId;
                $pk->fragmentIndex = ++$index;
                $this->sendQueue[$time] = [
                    "sequenceNumber" => $pk->sequenceNumber,
                    "packet" => $pk
                ];
                $pk->body = $buffer;
                $offset += $fragmentSize;
            }
            if($remainder > 0){
                $time = intval(round(microtime(true), 8) * 100000000);
                $pk = new FrameSetPacket(0x80, substr($buffer, $offset, $remainder));
                $pk->sequenceNumber = $rangeEnd + 1;
                $pk->flags = 0b11110000;
                $pk->length = strlen($buffer) << 3;
                $pk->reliableFrameIndex = $rangeEnd + 1;
                $pk->fragmentCompoundSize = $compoundSize;
                $pk->fragmentCompoundId = $compoundId;
                $pk->fragmentIndex = ++$index;
                $this->sendQueue[$time] = [
                    "sequenceNumber" => $pk->sequenceNumber,
                    "packet" => $pk
                ];
                $pk->body = $buffer;
            }
        } else {
            // Add the packet to queue
            regenSeqNum:
            $regen = false;
            $seqNum = mt_rand(0, (PHP_INT_MAX - 100000));
            foreach($this->sendQueue as $p){
                if($p["sequenceNumber"] === $seqNum){
                    $regen = true;
                    break;
                }
            }
            if($regen) goto regenSeqNum;

            $time = intval(round(microtime(true), 8) * 100000000);
            $pk = new FrameSetPacket(0x80, $packet->make());
            $pk->sequenceNumber = 0;
            $pk->flags = 0b10000000;
            $pk->length = strlen($buffer) << 3;
            $pk->reliableFrameIndex = 0;
            $this->sendQueue[$time] = [
                "sequenceNumber" => $pk->sequenceNumber,
                "packet" => $pk
            ];
            $pk->body = $buffer;
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
     * @return void
     * @throws Exception
     */
    public function sendPackets(): void {
        ksort($this->sendQueue);
        foreach($this->sendQueue as $pk){
            $packet = $pk["packet"];
            $buffer = $packet->make();
            $data["sequenceNumber"] = $packet->getSequenceNumber();
            $data["flags"] = (($packet->getFlags() & 0b11110000) >> 4);
            $data["reliable"] = $packet->isReliable();
            if($packet->isReliable()) $data["reliableFrameIndex"] = $packet->getReliableFrameIndex();
            $data["sequenced"] = $packet->isSequenced();
            if($packet->isSequenced()) $data["sequencedFrameIndex"] = $packet->getSequencedFrameIndex();
            $data["ordered"] = $packet->isOrdered();
            if($packet->isOrdered()){
                $data["orderFrameIndex"] = $packet->getOrderFrameIndex();
                $data["orderChannel"] = $packet->getOrderChannel();
            }
            $data["fragmented"] = $packet->isFragmented();
            if($packet->isFragmented()){
                $data["fragmentCompoundSize"] = $packet->getFragmentCompoundSize();
                $data["fragmentCompoundId"] = $packet->getFragmentCompoundId();
                $data["fragmentIndex"] = $packet->getFragmentIndex();
            }
            $data["length"] = $packet->getLength();
            $data["body"] = $packet->getBody();
            var_dump($data);
            ServerSocket::getInstance()->write($buffer, $this->getAddress()->getIp(), $this->getAddress()->getPort());
            CoreServer::getInstance()->getLogger()->info("Packet sent: 0x" . dechex(ord($packet->getBody()[0])));
        }
    }

    /**
     * @param string $buffer
     * @return void
     * @throws Exception
     */
    public function handle(string $buffer): void {
        $pid = ord($buffer[0]);
        CoreServer::getInstance()->getLogger()->info("Connected packet received: 0x" . dechex($pid));

        // Received FrameSetPacket
        if($pid >= 0x80 and $pid <= 0x8d){
            $packet = new FrameSetPacket($pid, $buffer);
            $packet->extract();
            $this->handleFrameSet($packet);
        }

        // Received reliability packet
        if($pid === 0xc0 or $pid === 0xa0){
            $packet = new ACK($pid, $buffer);
            $packet->extract();
            $this->receiveReliability($packet);
        }

        // Send queued packets
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
        $data["sequenceNumber"] = $packet->getSequenceNumber();
        $data["flags"] = decbin(($packet->getFlags() | 0b00000000) >> 3);
        $data["reliable"] = $packet->isReliable();
        if($packet->isReliable()) $data["reliableFrameIndex"] = $packet->getReliableFrameIndex();
        $data["sequenced"] = $packet->isSequenced();
        if($packet->isSequenced()) $data["sequencedFrameIndex"] = $packet->getSequencedFrameIndex();
        $data["ordered"] = $packet->isOrdered();
        if($packet->isOrdered()){
            $data["orderFrameIndex"] = $packet->getOrderFrameIndex();
            $data["orderChannel"] = $packet->getOrderChannel();
        }
        $data["fragmented"] = $packet->isFragmented();
        if($packet->isFragmented()){
            $data["fragmentCompoundSize"] = $packet->getFragmentCompoundSize();
            $data["fragmentCompoundId"] = $packet->getFragmentCompoundId();
            $data["fragmentIndex"] = $packet->getFragmentIndex();
        }
        $data["length"] = $packet->getLength();
        $data["body"] = $packet->getBody();
        var_dump($data);

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
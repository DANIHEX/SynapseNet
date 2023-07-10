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
use synapsenet\network\protocol\raknet\ReliabilityType;
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
            CoreServer::getInstance()->getLogger()->info("Fragmenting...");
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
                $pk = new FrameSetPacket();
                $pk->sequenceNumber = $seqNum;
                $pk->reliabilityId = ReliabilityType::RELIABLE;
                $pk->length = strlen($buffer);
                $pk->reliableFrameIndex = $seqNum;
                $pk->fragmentCompoundSize = $compoundSize;
                $pk->fragmentCompoundId = $compoundId;
                $pk->fragmentIndex = ++$index;
                $pk->body = substr($buffer, $offset, $fragmentSize);
                $this->sendQueue[$time] = [
                    "sequenceNumber" => $pk->sequenceNumber,
                    "packet" => $pk
                ];
                $offset += $fragmentSize;
            }
            if($remainder > 0){
                $time = intval(round(microtime(true), 8) * 100000000);
                $pk = new FrameSetPacket();
                $pk->sequenceNumber = $rangeEnd + 1;
                $pk->reliabilityId = ReliabilityType::RELIABLE;
                $pk->length = strlen($buffer);
                $pk->reliableFrameIndex = $rangeEnd + 1;
                $pk->fragmentCompoundSize = $compoundSize;
                $pk->fragmentCompoundId = $compoundId;
                $pk->fragmentIndex = ++$index;
                $pk->body = substr($buffer, $offset, $remainder);
                $this->sendQueue[$time] = [
                    "sequenceNumber" => $pk->sequenceNumber,
                    "packet" => $pk
                ];
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
            $pk = new FrameSetPacket();
            $pk->sequenceNumber = 888;
            $pk->reliabilityId = ReliabilityType::RELIABLE;
            $pk->length = strlen($buffer);
            $pk->body = $buffer;
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
        CoreServer::getInstance()->getLogger()->info("Removed from queue: " . $sequenceNumber);
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
            var_dump($packet->getData());
            $buffer = $packet->make();
            $npk = new FrameSetPacket(0x80, $buffer);
            $npk->extract();
            var_dump($npk->getData());
            ServerSocket::getInstance()->write($buffer, $this->getAddress()->getIp(), $this->getAddress()->getPort());
            CoreServer::getInstance()->getLogger()->info("Packet sent: 0x" . dechex(ord($npk->getBody()[0])));
        }
    }

    /**
     * @param string $buffer
     * @return void
     * @throws Exception
     */
    public function handle(string $buffer): void {
        $pid = ord($buffer[0]);

        // Received FrameSetPacket
        if($pid >= 0x80 and $pid <= 0x8d){
            $packet = new FrameSetPacket($pid, $buffer);
            $packet->extract();
            $this->handleFrameSet($packet);
        }

        // Received reliability packet
        if($pid === 0xc0 or $pid === 0xa0){
            CoreServer::getInstance()->getLogger()->info("Reliability packet received: 0x" . dechex($pid) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL);
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
                    new Address(4, CoreServer::getInstance()->getIp(), CoreServer::getInstance()->getPort()),
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
                $pk->time = intval(microtime(true) * 1000);
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
        CoreServer::getInstance()->getLogger()->info("Frame set packet received: " . dechex(ord($packet->getBody()[0])));

        var_dump($packet->getData());

        if(!$this->sendReliability($packet, $pid)) return;
        CoreServer::getInstance()->getLogger()->info("Reliability packet sent: 0x" . dechex($pid) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL);

        if($packet->isFragmented()){
             $this->handleFragmented($packet);
        } else {
            $this->handlePacket(RaknetPacketMap::match($packet->getBody()));
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
     * @param $pid
     * @return bool
     * @throws Exception
     */
    public function sendReliability(FrameSetPacket $packet, &$pid): bool {
        $reliable = false;
        $record = [
            "sequenceNumber" => $packet->getSequenceNumber()
        ];
        if($packet->isReliable()){
            $pid = RaknetPacketIds::ACK;
            $pk = new ACK($pid);
            $reliable = true;
        } else {
            $pid = RaknetPacketIds::NACK;
            $pk = new ACK($pid);
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
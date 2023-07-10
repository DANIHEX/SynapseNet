<?php

namespace synapsenet\network\protocol\raknet;

use Exception;
use synapsenet\core\CoreServer;
use synapsenet\network\Address;
use synapsenet\network\Connection;
use synapsenet\network\Network;
use synapsenet\network\protocol\PacketHandler;
use synapsenet\network\protocol\raknet\packets\OpenConnectionReply1;
use synapsenet\network\protocol\raknet\packets\OpenConnectionReply2;
use synapsenet\network\protocol\raknet\packets\OpenConnectionRequest1;
use synapsenet\network\protocol\raknet\packets\OpenConnectionRequest2;
use synapsenet\network\protocol\raknet\packets\UnconnectedPing;
use synapsenet\network\protocol\raknet\packets\UnconnectedPong;
use synapsenet\network\protocol\raknet\packets\UnknownPacket;

class Handshake {

    /**
     * @var PacketHandler
     */
    public PacketHandler $handler;

    /**
     * @var array
     */
    public array $temp = [];

    /**
     * @param PacketHandler $handler
     */
    public function __construct(PacketHandler $handler){
        $this->handler = $handler;
    }

    /**
     * @return PacketHandler
     */
    public function getHandler(): PacketHandler {
        return $this->handler;
    }

    /**
     * @return int
     */
    public function getRandomId(): int {
        return Network::getInstance()->getRandomId();
    }

    /**
     * @param Address $address
     * @param string $buffer
     * @return void
     * @throws Exception
     */
    public function handle(Address $address, string $buffer): void {
        $packet = RaknetPacketMap::match($buffer);

        // Ignore unknown unconnected packets
        // TODO: Or maybe handle them later
        if($packet instanceof UnknownPacket){
            CoreServer::getInstance()->getLogger()->info("Unknown packet received: " . $packet->getPacketId());
            return;
        }

//        CoreServer::getInstance()->getLogger()->info("Unconnected packet received: " . $packet->getPacketId());

        switch($packet->getPacketId()){
            case RaknetPacketIds::UNCONNECTED_PING:
            case RaknetPacketIds::UNCONNECTED_PING_OPEN:
                /** @var UnconnectedPing $packet */
                $this->handleUnconnectedPing($address, $packet);
                break;
            case RaknetPacketIds::OPEN_CONNECTION_REQUEST_1:
                /** @var OpenConnectionRequest1 $packet */
                $this->handleOpenConnectionRequest1($address, $packet);
                break;
            case RaknetPacketIds::OPEN_CONNECTION_REQUEST_2:
                /** @var OpenConnectionRequest2 $packet */
                $this->handleOpenConnectionRequest2($address, $packet);
                $this->openConnection($address);
                CoreServer::getInstance()->getLogger()->info(PHP_EOL . PHP_EOL . "Connection opened for " . $address->string() . PHP_EOL . PHP_EOL);
                break;
        }
    }

    /**
     * @param Address $address
     * @param UnconnectedPing $packet
     * @return void
     */
    public function handleUnconnectedPing(Address $address, UnconnectedPing $packet): void {
        $pk = new UnconnectedPong();
        $pk->time = $packet->getTime();
        $pk->serverGuid = $this->getRandomId();
        $pk->magic = $packet->getMagic();
        $pk->serverIdString = CoreServer::getInstance()->getQuery()->getQueryString();
        $this->getHandler()->send($address, $pk);
    }

    /**
     * @param Address $address
     * @param OpenConnectionRequest1 $packet
     * @return void
     */
    public function handleOpenConnectionRequest1(Address $address, OpenConnectionRequest1 $packet): void {
        Network::getInstance()->setMtuSize($packet->getMtuSize());
        $this->temp[$address->string()]["protocol"] = $packet->getProtocol();

        $pk = new OpenConnectionReply1();
        $pk->magic = $packet->getMagic();
        $pk->serverGuid = $this->getRandomId();
        $pk->useSecurity = false;
        $pk->mtuSize = $packet->getMtuSize();
        $this->getHandler()->send($address, $pk);
    }

    /**
     * @param Address $address
     * @param OpenConnectionRequest2 $packet
     * @return void
     */
    public function handleOpenConnectionRequest2(Address $address, OpenConnectionRequest2 $packet): void {
        $this->temp[$address->string()]["guid"] = $packet->getClientGuid();

        $pk = new OpenConnectionReply2();
        $pk->magic = $packet->getMagic();
        $pk->serverGuid = $this->getRandomId();
        $pk->clientAddress = $address;
        $pk->mtuSize = $packet->getMtuSize();
        $pk->encryptionEnabled = false;
        $this->getHandler()->send($address, $pk);
    }

    /**
     * @param Address $address
     * @return void
     * @throws Exception
     */
    public function openConnection(Address $address): void {
        if(isset($this->temp[$address->string()])){
            $protocol = $this->temp[$address->string()]["protocol"];
            $guid = $this->temp[$address->string()]["guid"];
            Network::getInstance()->getConnectionManager()->connect(new Connection($address, $protocol, $guid));
            unset($this->temp[$address->string()]);
        }
    }

}
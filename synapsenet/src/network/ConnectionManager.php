<?php

namespace synapsenet\network;

use Exception;

class ConnectionManager {

    /**
     * @var array
     */
    public array $pool = [];

    public function __construct() {
        
    }

    /**
     * @param Connection $connection
     * @return void
     * @throws Exception
     */
    public function connect(Connection $connection): void {
        if(isset($this->pool[$connection->getAddress()->string()])){
            throw new Exception("Address [" . $connection->getAddress()->string() . "] is already connected.");
        }
        $this->pool[$connection->getAddress()->string()] = $connection;
    }

    /**
     * The disconnection should be handled on connection itself
     * this just removes the connection from the pool not disconnecting it
     *
     * @param Connection $connection
     * @return void
     * @throws Exception
     */
    public function disconnect(Connection $connection): void {
        if(!isset($this->pool[$connection->getAddress()->string()])){
            throw new Exception("Address [" . $connection->getAddress()->string() . "] is not connected.");
        }
        unset($this->pool[$connection->getAddress()->string()]);
    }

    /**
     * @param Address $address
     * @return bool
     */
    public function isConnected(Address $address): bool {
        return isset($this->pool[$address->string()]);
    }

    /**
     * @param Address $address
     * @return Connection|null
     */
    public function getConnection(Address $address): ?Connection {
        if(isset($this->pool[$address->string()])){
            return $this->pool[$address->string()];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getPool(): array {
        return $this->pool;
    }

}
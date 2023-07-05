<?php

namespace synapsenet\network;

class ConnectionManager {

    /**
     * @var array
     */
    public array $pool = [];

    public function __construct() {
        
    }

    public function isConnected(Address $address): bool {
        return isset($this->pool[$address->string()]);
    }

    /**
     * @param Connection $connection
     * @return void
     */
    public function addNewConnection(Connection $connection): void {
        $this->pool[$connection->getAddress()->string()] = $connection;
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
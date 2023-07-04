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
     * @param int $guid
     * @return Connection|null
     */
    public function getConnectionByGuid(int $guid): ?Connection {
        if(isset($this->pool[$guid])){
            return $this->pool[$guid];
        }
        return null;
    }

    /**
     * @param int $protocol
     * @return array
     */
    public function getConnectionsByProtocol(int $protocol = 11): array {
        $connections = [];
        foreach($this->getPool() as $guid => $connection){
            if($connection->getProtocol() === $protocol){
                $connections[$guid] = $connection;
            }
        }
        return $connections;
    }

    /**
     * @return array
     */
    public function getPool(): array {
        return $this->pool;
    }

}
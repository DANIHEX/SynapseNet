<?php

namespace synapsenet\network;

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
     */
    public function addNewConnection(Connection $connection): void {
        $this->pool[$connection->getGuid()] = $connection;
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
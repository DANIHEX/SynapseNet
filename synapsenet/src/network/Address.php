<?php

declare(strict_types = 1);

namespace synapsenet\network;

class Address {

    public int $version;

    public string $ip;

    public int $port;

    public function __construct(int $version, string $ip, int $port){
        $this->version = $version;
        $this->ip = $ip;
        $this->port = $port;
    }

    public function getVersion(): int {
        return $this->version;
    }

    public function getIp(): string {
        return $this->ip;
    }

    public function getport(): int {
        return $this->port;
    }

}

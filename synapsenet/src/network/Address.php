<?php

declare(strict_types=1);

namespace synapsenet\network;

class Address {

    /** @var int */
    public int $version;

    /** @var string */
    public string $ip;

    /** @var int */
    public int $port;

    /**
     * @param int $version
     * @param string $ip
     * @param int $port
     */
    public function __construct(int $version, string $ip, int $port) {
        $this->version = $version;
        $this->ip = $ip;
        $this->port = $port;
    }

    /**
     * @return int
     */
    public function getVersion(): int {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getIp(): string {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getPort(): int {
        return $this->port;
    }

    /**
     * @return string
     */
    public function string(): string {
        return $this->getIp() . ":" . $this->getPort();
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->getIp() . ":" . $this->getPort();
    }
}

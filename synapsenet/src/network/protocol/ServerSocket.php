<?php

/**
 *
 *  ______   ___   _    _    ____  ____  _____ _   _ _____ _____ 
 * / ___\ \ / / \ | |  / \  |  _ \/ ___|| ____| \ | | ____|_   _|
 * \___ \\ V /|  \| | / _ \ | |_) \___ \|  _| |  \| |  _|   | |  
 *  ___) || | | |\  |/ ___ \|  __/ ___) | |___| |\  | |___  | |  
 * |____/ |_| |_| \_/_/   \_\_|   |____/|_____|_| \_|_____| |_|  
 *
 *
 *
 *
*/

declare(strict_types=1);

namespace synapsenet\network\protocol;

use Exception;
use synapsenet\core\CoreServer;

class ServerSocket {

    /** @var string */
    public string $address;

    /** @var int */
    public int $port;

    /** @var resource */
    public $socket;

    /**
     * @param string $address
     * @param int $port
     * @param int $version
     */
    public function __construct(string $address, int $port, int $version) {
        $this->address = $address;

        if($port < 0 or $port > 65535) {
            throw new Exception("Only ports in range 0 to 65535 are accepted");
        }

        if($port < 11) {
            throw new Exception("Ports 0 to 10 are for internal uses");
        }

        $this->port = $port;

        if($version === 4) {
            $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        } else {
            $socket = socket_create(AF_INET6, SOCK_DGRAM, SOL_UDP);
        }

        if(!$socket) {
            throw new Exception("Socket creation failed due to: " . socket_strerror(socket_last_error()));
        }

        $this->socket = $socket;

        if($version === 6) {
            socket_set_option($this->socket, IPPROTO_IPV6, IPV6_V6ONLY, 1);
        }

        CoreServer::getInstance()->getLogger()->info("Address: " . $address . ":" . $port);
        $bind = socket_bind($this->socket, $address, $port);

        if(!$bind) {
            if(socket_last_error($this->socket) === SOCKET_EADDRINUSE ) {
                throw new Exception("Selected port is currently being used by another program");
            }

            throw new Exception("Socket binding failed due to: " . socket_strerror(socket_last_error()));
        }

        $send = 8 * 1024 * 1024;
        $recieve = 8 * 1024 * 1024;
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDBUF, $send);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVBUF, $recieve);

        socket_set_nonblock($this->socket);
    }

    /**
     * @param string $buffer
     * @param string $address
     * @param int $port
     *
     * @return void
     */
    public function test(string $buffer, string $address, int $port): void {
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_sendto($socket, $buffer, strlen($buffer), 0, $address, $port);
    }

    /**
     * @param string|null $buffer
     * @param string|null $source
     * @param int|null $port
     *
     * @return bool|int
     */
    public function read(?string &$buffer, ?string &$source, ?int &$port) {
        return socket_recvfrom($this->socket, $buffer, 65535, 0, $source, $port);
    }

    /**
     * @param string $buffer
     * @param string $dest
     * @param int $port
     *
     * @return bool|int
     */
    public function write(string $buffer, string $dest, int $port) {
        return socket_sendto($this->socket, $buffer, strlen($buffer), 0, $dest, $port);
    }
}

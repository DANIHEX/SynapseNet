<?php

declare(strict_types = 1);

namespace synapsenet\network\protocol;

class ServerSocket {

    public string $address;
    public int $port;
    public $socket;

    public function __construct(string $ip, int $port, int $version){
        $this->address = $ip;

        if($port < 0 or $port > 65535){
            throw new \InvalidArgumentException("Invalid port range: " . $port . ", A valid port should be between 0 to 65535.");
        }
        $this->port = $port;

        $socket = @socket_create($version === 4 ? AF_INET : AF_INET6, SOCK_DGRAM, SOL_UDP);
        if(!$socket){
            throw new \RuntimeException("Failed to create socket: " . trim(socket_strerror(socket_last_error())));
        }
        $this->socket = $socket;

        if($version === 6){
            socket_set_option($this->socket, IPPROTO_IPV6, IPV6_V6ONLY, 1);
        }

        $bind = @socket_bind($this->socket, $ip, $port);
        if($bind){
            $size = 1024 * 1024 * 8;
            @socket_set_option($this->socket, SOL_SOCKET, SO_SNDBUF, $size);
            @socket_set_option($this->socket, SOL_SOCKET, SO_RCVBUF, $size);
        } else {
            $error = socket_last_error($this->socket);
            if($error === SOCKET_EADDRINUSE){
                throw new \RuntimeException("Failed to bind socket: Something else is already running on " . $ip . ":" . $port);
            }
            throw new \RuntimeException("Failed to bind to " . $ip . ":" . $port . ": " . trim(socket_strerror(socket_last_error($this->socket))));
        }

        socket_set_nonblock($this->socket);
    }

    public function read(?string &$buffer, ?string &$source, ?int &$port) {
        return @socket_recvfrom($this->socket, $buffer, 65535, 0, $source, $port);
    }

    public function write(string $buffer, string $dest, int $port) {
        return socket_sendto($this->socket, $buffer, strlen($buffer), 0, $dest, $port);
    }

}

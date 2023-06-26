<?php

declare(strict_types=1);

namespace synapsenet\binary;

use Exception;
use synapsenet\network\Address;

class Buffer {

    /** @var int */
    public int $offset;

    /** @var string */
    protected string $buffer;

    /**
     * @param string $buffer
     * @param int $offset
     */
    public function __construct(string $buffer = "", int $offset = 0) {
        $this->buffer = $buffer;
        $this->offset = $offset;
    }

    /**
     * @param $len
     *
     * @return string
     */
    public function get($len): string {
        if($len === 0) {
            return "";
        }

        $buflen = strlen($this->buffer);
        if($len === true) {
            $str = substr($this->buffer, $this->offset);
            $this->offset = $buflen;
            return $str;
        }

        if($len < 0) {
            $this->offset = $buflen - 1;
            return "";
        }

        $remaining = $buflen - $this->offset;
        if($remaining < $len) {
            throw new Exception("No more data left to read. needed " . $len . " but only " . $remaining . " remaining");
        }

        return $len === 1 ? $this->buffer[$this->offset++] : substr($this->buffer, ($this->offset += $len) - $len, $len);
    }

    public function getAddress(string $buffer): Address {
        $buffer = new Buffer($buffer);
        $version = ord($buffer->get(1));
        $ip = "0:0:0:0";
        $port = 0;
        if($version === 4){
            $ip = ord($buffer->get(1)) . ":" . ord($buffer->get(1)) . ":" . ord($buffer->get(1)) . ":" . ord($buffer->get(1));
            $port = Binary::readShort($buffer->get(2));
        }

        return new Address($version, $ip, $port);
    }

    public function getAddressBuffer(Address $addres): string {
        $parts = explode(":", $addres->getIp());
        $buf = chr($addres->getVersion());
        $buf .= chr(intval($parts[0]));
        $buf .= chr(intval($parts[1]));
        $buf .= chr(intval($parts[2]));
        $buf .= chr(intval($parts[3]));
        $buf .= Binary::writeShort($addres->getport());
        return $buf;
    }

    public function getRemaining(){
        return substr($this->buffer, $this->offset);
    }
}

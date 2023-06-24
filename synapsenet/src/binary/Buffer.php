<?php

declare(strict_types = 1);

namespace synapsenet\binary;

use Exception;

class Buffer {

    protected string  $buffer;
    public int $offset;
    public int $lastOffset = 0;

    public function __construct(string $buffer, int $offset = 0){
        $this->buffer = $buffer;
        $this->offset = $offset;
    }

    public function get($len){
        if($len === 0){
            return "";
        }

        $buflen = strlen($this->buffer);
        if($len === true){
            $str = substr($this->buffer, $this->offset);
            $this->offset = $buflen;
            return $str;
        }
        if($len < 0){
            $this->offset = $buflen - 1;
            return "";
        }
        $remaining = $buflen - $this->offset;
        if($remaining < $len){
            throw new Exception("Not enough bytes left in buffer: need $len, have $remaining");
        }

        return $len === 1 ? $this->buffer[$this->offset++] : substr($this->buffer, ($this->offset += $len) - $len, $len);
    }

}

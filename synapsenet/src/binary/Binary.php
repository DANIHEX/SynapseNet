<?php

declare(strict_types = 1);

namespace synapsenet\binary;

use Exception;

class Binary {
	
    private const SIZEOF_SHORT = 2;
    private const SIZEOF_INT = 4;
    private const SIZEOF_LONG = 8;

    private const SIZEOF_FLOAT = 4;
    private const SIZEOF_DOUBLE = 8;

    public static function signByte(int $value): int {
        return $value << 56 >> 56;
    }

    public static function unsignByte(int $value): int {
        return $value & 0xff;
    }

    public static function signShort(int $value): int {
        return $value << 48 >> 48;
    }

    public static function unsignShort(int $value): int {
        return $value & 0xffff;
    }

    public static function signInt(int $value): int {
        return $value << 32 >> 32;
    }

    public static function unsignInt(int $value): int {
        return $value & 0xffffffff;
    }

    public static function flipShortEndianness(int $value): int {
        return self::readLShort(self::writeShort($value));
    }

    public static function flipIntEndianness(int $value): int {
        return self::readLInt(self::writeInt($value));
    }

    public static function flipLongEndianness(int $value): int {
        return self::readLLong(self::writeLong($value));
    }

    /**
    * @return mixed[]
    * @throws Exception
    */
    static function read(string $formatCode, string $bytes, int $needLength): array {
        $haveLength = strlen($bytes);
        if($haveLength < $needLength){
            throw new Exception("Not enough bytes: need $needLength, have $haveLength");
        }
        $result = unpack($formatCode, $bytes);
        if($result === false){
            throw new \AssertionError("unpack() failed for unknown reason");
       }
       return $result;
    }

    /**
    * Reads a byte boolean
    */
    public static function readBool(string $b) : bool{
        return $b[0] !== "\x00";
    }

    /**
    * Writes a byte boolean
    */
    public static function writeBool(bool $b) : string{
        return $b ? "\x01" : "\x00";
    }

	/**
	* Reads an unsigned byte (0 - 255)
	*
	* @throws Exception
	*/
	public static function readByte(string $c) : int{
		if($c === ""){
			throw new Exception("Expected a string of length 1");
		}
		return ord($c[0]);
	}

	/**
	* Reads a signed byte (-128 - 127)
	*
	* @throws Exception
	*/
	public static function readSignedByte(string $c) : int{
		if($c === ""){
			throw new Exception("Expected a string of length 1");
		}
		return self::signByte(ord($c[0]));
	}

	/**
	* Writes an unsigned/signed byte
	*/
	public static function writeByte(int $c) : string{
		return chr($c);
	}

	/**
	* Reads a 16-bit unsigned big-endian number
	*
	* @throws Exception
	*/
	public static function readShort(string $str) : int{
		return self::read("n", $str, self::SIZEOF_SHORT)[1];
	}

	/**
	* Reads a 16-bit signed big-endian number
	*
	* @throws Exception
	*/
	public static function readSignedShort(string $str) : int{
		return self::signShort(self::read("n", $str, self::SIZEOF_SHORT)[1]);
	}

	/**
	* Writes a 16-bit signed/unsigned big-endian number
	*/
	public static function writeShort(int $value) : string{
		return pack("n", $value);
	}

	/**
	* Reads a 16-bit unsigned little-endian number
	*
	* @throws Exception
	*/
	public static function readLShort(string $str) : int{
		return self::read("v", $str, self::SIZEOF_SHORT)[1];
	}

	/**
	* Reads a 16-bit signed little-endian number
	*
	* @throws Exception
	*/
	public static function readSignedLShort(string $str) : int{
		return self::signShort(self::read("v", $str, self::SIZEOF_SHORT)[1]);
	}

	/**
	* Writes a 16-bit signed/unsigned little-endian number
	*/
	public static function writeLShort(int $value) : string{
		return pack("v", $value);
	}

	/**
	* Reads a 3-byte big-endian number
	*
	* @throws Exception
	*/
	public static function readTriad(string $str) : int{
		return self::read("N", "\x00" . $str, self::SIZEOF_INT)[1];
	}

	/**
	* Writes a 3-byte big-endian number
	*/
	public static function writeTriad(int $value) : string{
		return substr(pack("N", $value), 1);
	}

	/**
	* Reads a 3-byte little-endian number
	*
	* @throws Exception
	*/
	public static function readLTriad(string $str) : int{
		return self::read("V", $str . "\x00", self::SIZEOF_INT)[1];
	}

	/**
	* Writes a 3-byte little-endian number
	*/
	public static function writeLTriad(int $value) : string{
		return substr(pack("V", $value), 0, -1);
	}

	/**
	* Reads a 4-byte signed integer
	*
	* @throws Exception
	*/
	public static function readInt(string $str) : int{
		return self::signInt(self::read("N", $str, self::SIZEOF_INT)[1]);
	}

	/**
	* Writes a 4-byte integer
	*/
	public static function writeInt(int $value) : string{
		return pack("N", $value);
	}

	/**
	* Reads a 4-byte signed little-endian integer
	*
	* @throws Exception
	*/
	public static function readLInt(string $str) : int{
		return self::signInt(self::read("V", $str, self::SIZEOF_INT)[1]);
	}

	/**
	* Writes a 4-byte signed little-endian integer
	*/
	public static function writeLInt(int $value) : string{
		return pack("V", $value);
	}

	/**
	* Reads a 4-byte floating-point number
	*
	* @throws Exception
	*/
	public static function readFloat(string $str) : float{
		return self::read("G", $str, self::SIZEOF_FLOAT)[1];
	}

	/**
	* Reads a 4-byte floating-point number, rounded to the specified number of decimal places.
	*
	* @throws Exception
	*/
	public static function readRoundedFloat(string $str, int $accuracy) : float{
		return round(self::readFloat($str), $accuracy);
	}

	/**
	* Writes a 4-byte floating-point number.
	*/
	public static function writeFloat(float $value) : string{
		return pack("G", $value);
	}

	/**
	* Reads a 4-byte little-endian floating-point number.
	*
	* @throws Exception
	*/
	public static function readLFloat(string $str) : float{
		return self::read("g", $str, self::SIZEOF_FLOAT)[1];
	}

	/**
	* Reads a 4-byte little-endian floating-point number rounded to the specified number of decimal places.
	*
	* @throws Exception
	*/
	public static function readRoundedLFloat(string $str, int $accuracy) : float{
		return round(self::readLFloat($str), $accuracy);
	}

	/**
	* Writes a 4-byte little-endian floating-point number.
	*/
	public static function writeLFloat(float $value) : string{
		return pack("g", $value);
	}

	/**
	* Returns a printable floating-point number.
	*/
	public static function printFloat(float $value) : string{
		return preg_replace("/(\\.\\d+?)0+$/", "$1", sprintf("%F", $value));
	}

	/**
	* Reads an 8-byte floating-point number.
	*
	* @throws Exception
	*/
	public static function readDouble(string $str) : float{
		return self::read("E", $str, self::SIZEOF_DOUBLE)[1];
	}

	/**
	* Writes an 8-byte floating-point number.
	*/
	public static function writeDouble(float $value) : string{
		return pack("E", $value);
	}

	/**
	* Reads an 8-byte little-endian floating-point number.
	*
	* @throws Exception
	*/
	public static function readLDouble(string $str) : float{
		return self::read("e", $str, self::SIZEOF_DOUBLE)[1];
	}

	/**
	* Writes an 8-byte floating-point little-endian number.
	*/
	public static function writeLDouble(float $value) : string{
		return pack("e", $value);
	}

	/**
	* Reads an 8-byte integer.
	*
	* @throws Exception
	*/
	public static function readLong(string $str) : int{
		return self::read("J", $str, self::SIZEOF_LONG)[1];
	}

	/**
	* Writes an 8-byte integer.
	*/
	public static function writeLong(int $value) : string{
		return pack("J", $value);
	}

	/**
	* Reads an 8-byte little-endian integer.
	*
	* @throws Exception
	*/
	public static function readLLong(string $str) : int{
		return self::read("P", $str, self::SIZEOF_LONG)[1];
	}

	    /**
    * Converts a binary string to a binary array.
    *
    * @param string $binaryString The binary string to convert.
    * @return int[] The binary array.
    */
    public static function binaryStringToArray(string $binaryString): array {
        $binaryArray = [];
        $length = strlen($binaryString);
        
        for ($i = 0; $i < $length; $i++) {
            $binaryArray[] = ord($binaryString[$i]);
        }
        
        return $binaryArray;
    }

    /**
    * Converts a binary array to a binary string.
    *
    * @param int[] $binaryArray The binary array to convert.
    * @return string The binary string.
    */
    public static function binaryArrayToString(array $binaryArray): string {
        $binaryString = '';
        
        foreach ($binaryArray as $byte) {
            $binaryString .= chr($byte);
        }
        
        return $binaryString;
    }

	/**
	* Writes an 8-byte little-endian integer.
	*/
	public static function writeLLong(int $value) : string{
		return pack("P", $value);
	}

	/**
	* Reads a 32-bit zigzag-encoded variable-length integer.
	*
	* @param int    $offset reference parameter
	*
	* @throws Exception
	*/
	public static function readVarInt(string $buffer, int &$offset) : int{
		$raw = self::readUnsignedVarInt($buffer, $offset);
		$temp = ((($raw << 63) >> 63) ^ $raw) >> 1;
		return $temp ^ ($raw & (1 << 63));
	}

	/**
	* Reads a 32-bit variable-length unsigned integer.
	*
	* @param int    $offset reference parameter
	*
	* @throws Exception if the var-int did not end after 5 bytes or there were not enough bytes
	*/
	public static function readUnsignedVarInt(string $buffer, int &$offset) : int{
		$value = 0;
		for($i = 0; $i <= 28; $i += 7){
			if(!isset($buffer[$offset])){
				throw new Exception("No bytes left in buffer");
			}
			$b = ord($buffer[$offset++]);
			$value |= (($b & 0x7f) << $i);

			if(($b & 0x80) === 0){
				return $value;
			}
		}

		throw new Exception("VarInt did not terminate after 5 bytes!");
	}

	/**
	* Writes a 32-bit integer as a zigzag-encoded variable-length integer.
	*/
	public static function writeVarInt(int $v) : string{
		$v = ($v << 32 >> 32);
		return self::writeUnsignedVarInt(($v << 1) ^ ($v >> 31));
	}

	/**
	* Writes a 32-bit unsigned integer as a variable-length integer.
	*
	* @return string up to 5 bytes
	*/
	public static function writeUnsignedVarInt(int $value) : string{
		$buf = "";
		$remaining = $value & 0xffffffff;
		for($i = 0; $i < 5; ++$i){
			if(($remaining >> 7) !== 0){
				$buf .= chr($remaining | 0x80);
			}else{
				$buf .= chr($remaining & 0x7f);
				return $buf;
			}

			$remaining = (($remaining >> 7) & (PHP_INT_MAX >> 6)); //PHP really needs a logical right-shift operator
		}

		throw new Exception("Value too large to be encoded as a VarInt");
	}

	/**
	* Reads a 64-bit zigzag-encoded variable-length integer.
	*
	* @param int    $offset reference parameter
	*
	* @throws Exception
	*/
	public static function readVarLong(string $buffer, int &$offset) : int{
		$raw = self::readUnsignedVarLong($buffer, $offset);
		$temp = ((($raw << 63) >> 63) ^ $raw) >> 1;
		return $temp ^ ($raw & (1 << 63));
	}

	/**
	* Reads a 64-bit unsigned variable-length integer.
	*
	* @param int    $offset reference parameter
	*
	* @throws Exception if the var-int did not end after 10 bytes or there were not enough bytes
	*/
	public static function readUnsignedVarLong(string $buffer, int &$offset) : int{
		$value = 0;
		for($i = 0; $i <= 63; $i += 7){
			if(!isset($buffer[$offset])){
				throw new Exception("No bytes left in buffer");
			}
			$b = ord($buffer[$offset++]);
			$value |= (($b & 0x7f) << $i);

			if(($b & 0x80) === 0){
				return $value;
			}
		}

		throw new Exception("VarLong did not terminate after 10 bytes!");
	}

	/**
	* Writes a 64-bit integer as a zigzag-encoded variable-length long.
	*/
	public static function writeVarLong(int $v) : string{
		return self::writeUnsignedVarLong(($v << 1) ^ ($v >> 63));
	}

	/**
	* Writes a 64-bit unsigned integer as a variable-length long.
	*/
	public static function writeUnsignedVarLong(int $value) : string{
		$buf = "";
		$remaining = $value;
		for($i = 0; $i < 10; ++$i){
			if(($remaining >> 7) !== 0){
				$buf .= chr($remaining | 0x80); //Let chr() take the last byte of this, it's faster than adding another & 0x7f.
			}else{
				$buf .= chr($remaining & 0x7f);
				return $buf;
			}

			$remaining = (($remaining >> 7) & (PHP_INT_MAX >> 6)); //PHP really needs a logical right-shift operator
		}

		throw new Exception("Value too large to be encoded as a VarLong");
	}

}

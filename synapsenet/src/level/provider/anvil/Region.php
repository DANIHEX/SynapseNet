<?php

namespace synapsenet\level\provider\anvil;

use synapsenet\binary\Binary;
use synapsenet\level\provider\anvil\types\RegionIndex;

class Region {
    private $stream;
    private $freeIndexes;
    private $path;

    public function __construct($path) {
        $this->path = $path;
        $this->freeIndexes = [];

        try {
            $fileContents = file_get_contents($this->path);
            $this->stream = new Binary($fileContents);
        } catch (Exception $e) {
            $this->stream = new Binary(str_repeat("\x00", 8192));
        }
    }

    public function save() {
        try {
            file_put_contents($this->path, $this->stream->getBuffer());
        } catch (Exception $e) {
            echo "Failed to write region file " . $this->path;
        }
    }

    public function readIndex($x, $z) {
        $this->stream->setOffset((($x & 31) + (($z & 31) << 5)) << 2);
        $index = new RegionIndex();
        $index->offset = $this->stream->readUnsignedTriadBE();
        $index->length = $this->stream->readUnsignedByte();
        return $index;
    }

    public function writeIndex($x, $z, $offset, $length) {
        $stream = new Binary();
        $stream->writeUnsignedTriadBE($offset);
        $stream->writeUnsignedByte($length);
        $streamBuffer = $stream->getBuffer();
        $this->stream->write($streamBuffer, (($x & 31) + (($z & 31) << 5)) << 2, 4);
    }

    public function readChunkData($x, $z) {
        $index = $this->readIndex($x, $z);
        $this->stream->setOffset($index->offset << 12);
        if ($index->length) {
            $length = $this->stream->readIntBE();
            if (($length + 4 <= ($index->length << 12)) && $length !== 0) {
                $compressionType = $this->stream->readByte();
                $data = $this->stream->read($length - 1);
                if ($compressionType == 1) {
                    return gzdecode($data);
                }
                if ($compressionType == 2) {
                    return gzinflate($data);
                }
                if ($compressionType == 3) {
                    return $data;
                }
            }
        }
        return null;
    }

    public function writeChunkData($x, $z, $data, $compressionType) {
        $temp = new Binary();
        $temp->writeIntBE(strlen($data) + 1);
        $temp->writeByte($compressionType);
        if ($compressionType == 1) {
            $temp->write(gzencode($data));
        } elseif ($compressionType == 2) {
            $temp->write(gzdeflate($data));
        } elseif ($compressionType == 3) {
            $temp->write($data);
        }
        $sectorCount = ($temp->getLength() >> 12) + 1;
        $index = $this->readIndex($x, $z);
        if ($sectorCount > $index->length) {
            $i = 0;
            while ($i < count($this->freeIndexes)) {
                $freeIndex = $this->freeIndexes[$i];
                if ($sectorCount <= $freeIndex->length) {
                    $tempBuffer = $temp->getBuffer();
                    $this->stream->write($tempBuffer, $freeIndex->offset << 12, $sectorCount << 12);
                    if ($sectorCount < $freeIndex->length) {
                        $newFreeIndex = new RegionIndex();
                        $newFreeIndex->offset = $freeIndex->offset + $sectorCount;
                        $newFreeIndex->length = $freeIndex->length - $sectorCount;
                        $this->freeIndexes[] = $newFreeIndex;
                    }
                    $this->writeIndex($freeIndex->offset, $freeIndex->length);
                    array_splice($this->freeIndexes, $i, 1);
                    break;
                }
                $i++;
            }
            if ($i === count($this->freeIndexes)) {
                $this->writeIndex($this->stream->getLength() << 12, $sectorCount);
                $this->stream->write($temp->getBuffer());
            }
            $this->freeIndexes[] = clone $index;
        } else {
            $tempBuffer = $temp->getBuffer();
            $this->stream->write($tempBuffer, $index->offset << 12, $sectorCount << 12);
            if ($sectorCount < $index->length) {
                $freeIndex = new RegionIndex();
                $freeIndex->offset = $index->offset + $sectorCount;
                $freeIndex->length = $index->length - $sectorCount;
                $this->freeIndexes[] = $freeIndex;
            }
            $this->writeIndex($index->offset, $sectorCount);
        }
    }
}

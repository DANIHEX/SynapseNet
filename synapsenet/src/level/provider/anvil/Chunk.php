<?php

namespace synapsenet\level\provider\anvil;

use synapsenet\binary\NBTBEBinaryStream;

class Chunk {
    /**
     * Chunk format version
     * @var int
     */
    public $dataVersion;
    /**
     * x position in the map
     * @var int
     */
    public $xPos;
    /**
     * z position in the map
     * @var int
     */
    public $zPos;
    /**
     * y position in the map
     * @var int
     */
    public $yPos;
    /**
     * step in the chunk generation process
     * @var string
     */
    public $status;
    /**
     * Last update time
     * @var int
     */
    public $lastUpdate;
    /**
     * list of sections
     * @var array
     */
    public $sections;

    /**
     * Initializes a chunk
     * @param int $x
     * @param int $z
     */
    public function __construct($x, $z) {
        $this->dataVersion = 0;
        $this->xPos = $x;
        $this->zPos = $z;
        $this->yPos = -2;
        $this->status = "full";
        $this->lastUpdate = 0;
        $this->sections = array();
    }

    /**
     * Loads up values from chunk data
     * @param string|null $data
     */
    public function loadChunkData($data) {
        if ($data !== null) {
            $stream = new NBTBEBinaryStream($data);
            $root = $stream->readRootTag();
            foreach ($root->value as $entry) {
                if ($entry->tagName === "DataVersion") {
                    $this->dataVersion = $entry->value;
                } elseif ($entry->tagName === "xPos") {
                    $this->xPos = $entry->value;
                } elseif ($entry->tagName === "zPos") {
                    $this->zPos = $entry->value;
                } elseif ($entry->tagName === "yPos") {
                    $this->yPos = $entry->value;
                } elseif ($entry->tagName === "Status") {
                    $this->status = $entry->value;
                } elseif ($entry->tagName === "LastUpdate") {
                    $this->lastUpdate = $entry->value;
                } elseif ($entry->tagName === "sections") {
                    // todo
                }
            }
        }
    }
}

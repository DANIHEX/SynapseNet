<?php

declare(strict_types=1);

namespace synapsenet\network\protocol\raknet\packets;

use Exception;
use synapsenet\binary\Binary;
use synapsenet\network\protocol\Packet;

class ACK extends Packet {

    /**
     * @var int
     */
    public int $recordCount;

    /**
     * @var array
     */
    public array $records = [];

    /**
     * @param int $id
     * @param string $buffer
     */
    public function __construct(int $id, string $buffer = ""){
        parent::__construct($id, $buffer);
    }

    /**
     * @return int
     */
    public function getRecordCount(): int {
        return $this->recordCount;
    }

    /**
     * @return int
     */
    public function getActualRecordCount(): int {
        return count($this->records);
    }

    /**
     * @return array
     */
    public function getRecords(): array {
        return $this->records;
    }

    /**
     * @param array $record
     * @param bool $single
     *
     * @return ACK
     *
     * @throws Exception
     */
    public function addRecord(array $record, bool $single = true): ACK {
        if($single){
            if(!isset($record["sequenceNumber"])){
                throw new Exception("Record array must contain 'sequenceNumber' while single sequenced");
            }
        } else {
            if(
                !isset($record["startSequenceNumber"])
                or !isset($record["endSequenceNumber"])
            ){
                throw new Exception("Record array must contain 'startSequenceNumber' and 'endSequenceNumber' while not single sequenced");
            }
        }
        $this->records[] = $record;
        return $this;
    }

    /**
     * @return ACK
     *
     * @throws Exception
     */
    public function extract(): ACK {
        $this->get(1);

        $this->recordCount = $count = Binary::readShort($this->get(2));

        for($i = 1; $i <= $count; $i++){
            $singleSequenced = Binary::readBool($this->get(1));
            if($singleSequenced){
                $sequenceNumber = Binary::readLTriad($this->get(3));
                $this->records[] = [
                    "sequenceNumber" => $sequenceNumber
                ];
            } else {
                $start = Binary::readLTriad($this->get(3));
                $end = Binary::readLTriad($this->get(3));
                $this->records[] = [
                    "startSequenceNumber" => $start,
                    "endSequenceNumber" => $end
                ];
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function make(): string {
        $buffer = chr($this->getPacketId());
        $buffer .= Binary::writeLTriad(count($this->records));
        foreach($this->records as $record){
            if(count($record) === 1){
                // Single sequenced
                $buffer .= Binary::writeLTriad($record["sequenceNumber"]);
            } else {
                // Not single sequenced
                $buffer .= Binary::writeLTriad($record["startSequenceNumber"]);
                $buffer .= Binary::writeLTriad($record["endSequenceNumber"]);
            }
        }
        return $buffer;
    }

}
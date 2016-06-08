<?php

namespace UUID\Model;

use DeltaUtils\Object\Prototype\StringableInterface;

class UuidComplexShortTables implements UuidComplexInterface
{
    protected $epoch = 1451317149374;

    protected $value;
    /** @var  \DateTime */
    protected $date;
    protected $shard;
    protected $table;
    protected $id;


    public function __construct($value = null, $epoch = null)
    {
        if (null !== $value) {
            $this->setValue($value);
        }
        if (null !== $epoch) {
            $this->setEpoch($epoch);
        }
    }

    /**
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param integer|string $value
     */
    public function setValue($value)
    {
        $this->date = null;
        $this->shard = null;
        $this->id = null;
        $this->table = null;
        $this->value = (integer)$value;
    }

    /**
     * @return mixed
     */
    public function getEpoch()
    {
        return $this->epoch;
    }

    /**
     * @param mixed $epoch
     */
    public function setEpoch($epoch)
    {
        $this->epoch = $epoch;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        if (null === $this->date) {
            $epoch = $this->getEpoch();
            $uuid = $this->getValue();
            $timestamp = $uuid >> 23;
            $timestamp = ($timestamp + $epoch) / 1000;
            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('UTC'));
            $date->setTimestamp($timestamp);
            $this->date = $date;
        }
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getShard()
    {
        if (null === $this->shard) {
            $uuid = $this->getValue();
            $this->shard = (($uuid << 41) >> 41) >> 19;
        }
        return $this->shard;
    }

    public function getTable()
    {
        if (null === $this->table) {
            $uuid = $this->getValue();
            $this->table = (($uuid << 45) >> 45) >> 10;
        }
        return $this->table;
    }

    public function getId()
    {
        if (null === $this->id) {
            $uuid = $this->getValue();
            $this->id = ($uuid << 54) >> 54;
        }
        return $this->id;
    }

    public function toHex()
    {
        return dechex($this->getValue());
    }

    function __toString()
    {
        return (string)$this->getValue();
    }
}

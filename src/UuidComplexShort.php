<?php

namespace Akademiano\UUID;

use Akademiano\Entity\Uuid;
use Carbon\Carbon;

class UuidComplexShort extends Uuid implements UuidComplexInterface
{
    protected $value;
    /** @var  \DateTime */
    protected $date;
    protected $shard;
    protected $id;
    protected $epoch = 1451317149374;


    public function __construct($value = null, $epoch = null)
    {
        if (null !== $epoch) {
            $this->setEpoch($epoch);
        }

        parent::__construct($value);
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
     * @return Carbon
     */
    public function getDate()
    {
        if (null === $this->date) {
            $epoch = $this->getEpoch();
            $uuid = $this->getValue();
            $timestamp = $uuid >> 23;
            $timestamp = ($timestamp + $epoch) / 1000;
            $date = Carbon::createFromTimestampUTC($timestamp);
            $this->date = $date;
        }
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getShard()
    {
        return $this->shard;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        if (null === $this->id) {
            $uuid = $this->getValue();
            $id = $uuid << 54;
            $id = $id >> 54;
            $this->id = $id;
        }
        return $this->id;
    }

    public function getInt()
    {
        return $this->getValue();
    }

    public function getHex()
    {
        return dechex($this->getInt());
    }

    public function __toString()
    {
        return (string)$this->getValue();
    }

    public function serialize()
    {
        return serialize([
            'value' => $this->getValue(),
            'epoch' => $this->getEpoch(),
        ]);
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->setEpoch($data['epoch']);
        $this->setValue($data['value']);
    }

    public function jsonSerialize()
    {
        return [
            'value' => $this->getHex(),
            'epoch' => $this->getEpoch(),
        ];
    }
}

<?php

namespace Akademiano\UUID;

use Akademiano\Entity\Uuid;
use Carbon\CarbonImmutable;

class UuidComplexShort extends Uuid implements UuidComplexInterface
{
    protected const EPOCH_FIELD = "epoch";

    /** @var  CarbonImmutable */
    protected $date;
    /** @var int */
    protected $shard;
    /** @var int */
    protected $id;
    /** @var int */
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
    public function getValue(): int
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
        parent::setValue($value);
    }

    public function getEpoch(): int
    {
        return $this->epoch;
    }

    /**
     * @param integer|string $epoch
     */
    public function setEpoch($epoch)
    {
        $this->epoch = (int)$epoch;
    }

    public function getDate(): \DateTimeImmutable
    {
        if (null === $this->date) {
            $epoch = $this->getEpoch();
            $uuid = $this->getValue();
            $timestamp = $uuid >> 23;
            $timestamp = (int)(($timestamp + $epoch) / 1000);
            $date = CarbonImmutable::createFromTimestampUTC($timestamp);
            $this->date = $date;
        }
        return $this->date;
    }

    public function getShard(): int
    {
        return $this->shard;
    }

    public function getId(): int
    {
        if (null === $this->id) {
            $uuid = $this->getValue();
            $this->id = ($uuid << 54) >> 54;
        }
        return $this->id;
    }

    public function serialize()
    {
        return serialize([
            self::VALUE_FIELD => $this->getValue(),
            self::EPOCH_FIELD => $this->getEpoch(),
        ]);
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->setEpoch($data[self::EPOCH_FIELD]);
        $this->setValue($data[self::VALUE_FIELD]);
    }

    public function jsonSerialize()
    {
        return [
            self::VALUE_FIELD => $this->getHex(),
            self::EPOCH_FIELD => $this->getEpoch(),
        ];
    }
}

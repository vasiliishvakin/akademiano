<?php

namespace Akademiano\UUID;

use Carbon\Carbon;

class UuidComplexShortTables extends UuidComplexShort
{
    /** @var int */
    protected $table;

    /**
     * @param integer|string $value
     */
    public function setValue($value)
    {
        $this->table = null;
        parent::setValue($value);
    }

    public function getShard(): int
    {
        if (null === $this->shard) {
            $uuid = $this->getValue();
            $this->shard = (($uuid << 41) >> 41) >> 19;
        }
        return $this->shard;
    }

    public function getTable(): int
    {
        if (null === $this->table) {
            $uuid = $this->getValue();
            $this->table = (($uuid << 45) >> 45) >> 10;
        }
        return $this->table;
    }
}

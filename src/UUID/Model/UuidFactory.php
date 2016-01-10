<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 10.01.16
 * Time: 17:36
 */

namespace UUID\Model;


class UuidFactory
{
    protected $epoch;

    /** @var UuidComplexInterface[] */
    protected $uuids = [];

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

    public function create($value, $epoch = null)
    {
        if (null === $epoch) {
            $epoch = $this->getEpoch();
        }
        $key = $value . $epoch;
        if (!isset($this->uuids[$key])) {
            $this->uuids[$key] = new UuidComplexShort($value, $epoch);
        }
        return $this->uuids[$key];
    }
}

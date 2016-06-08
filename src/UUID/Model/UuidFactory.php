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

    public function create($value, $epoch = null, $class = UuidComplexShortTables::class)
    {
        if (null === $epoch) {
            $epoch = $this->getEpoch();
        }
        if (!ctype_digit($value) && ctype_xdigit($value)) {
            $value = hexdec($value);
        }
        $key = $class . $value . $epoch;
        if (!isset($this->uuids[$key])) {
            switch ($class) {
                case UuidComplexShort::class : {
                    $this->uuids[$key] = new UuidComplexShort($value, $epoch);
                    break;
                }
                case UuidComplexShortTables::class : {
                    $this->uuids[$key] = new UuidComplexShortTables($value, $epoch);
                    break;
                }
                default:
                    throw new \InvalidArgumentException();
            }

        }
        return $this->uuids[$key];
    }
}

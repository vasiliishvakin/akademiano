<?php


namespace Akademiano\Utils\Paging;


use Akademiano\Utils\Exception\ReadOnlyAttemptChange;
use Akademiano\Utils\Object\Prototype\ArrayableInterface;

class Page implements \ArrayAccess, \JsonSerializable, ArrayableInterface
{
    protected $num;
    /** @var  bool */
    protected $isCurrent;

    protected $array;

    /**
     * Page constructor.
     * @param $num
     * @param bool $isCurrent
     */
    public function __construct($num, $isCurrent = false)
    {
        $this->num = $num;
        $this->isCurrent = $isCurrent;
    }


    /**
     * @return mixed
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * @return bool
     */
    public function isCurrent()
    {
        return $this->isCurrent;
    }

    public function toArray()
    {
        if (null === $this->array) {
            $this->array = [
                "num" => $this->getNum(),
                "isCurrent" => $this->isCurrent(),
            ];
        }
        return $this->array;
    }

    public function offsetExists($offset)
    {
        $array = $this->toArray();
        return (array_key_exists($offset, $array));
    }

    public function offsetGet($offset)
    {
        $array = $this->toArray();
        return $array[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new ReadOnlyAttemptChange("Could not set value for $offset in " . __CLASS__);
    }

    public function offsetUnset($offset)
    {
        throw new ReadOnlyAttemptChange("Could not unset value for $offset in " . __CLASS__);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}

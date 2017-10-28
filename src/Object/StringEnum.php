<?php


namespace Akademiano\Utils\Object;


use Akademiano\Utils\Object\Prototype\StringableInterface;
use MyCLabs\Enum\Enum;

class StringEnum extends Enum implements StringableInterface, \Serializable, \JsonSerializable
{


    public function serialize()
    {
        return serialize($this->__toString());
    }

    public function unserialize($serialized)
    {
        return $this->__construct(unserialize($serialized));
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }
}

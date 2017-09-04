<?php


namespace Akademiano\Utils\Object;

use Akademiano\Utils\Object\Prototype\IntegerableInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;
use MyCLabs\Enum\Enum;

abstract class IntegerEnum extends Enum implements StringableInterface, IntegerableInterface, \Serializable, \JsonSerializable
{
    public function getInt()
    {
        return (integer)$this->getValue();
    }

    public function serialize()
    {
        return serialize($this->getInt());
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

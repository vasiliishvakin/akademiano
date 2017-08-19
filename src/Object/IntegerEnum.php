<?php


namespace Akademiano\Utils\Object;

use Akademiano\Utils\Object\Prototype\IntegerableInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;

abstract class IntegerEnum extends \SplEnum implements StringableInterface, IntegerableInterface, \Serializable, \JsonSerializable
{
    public function getInt()
    {
        return (integer)$this;
    }

    public function __toString()
    {
        return (string)$this->getInt();
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

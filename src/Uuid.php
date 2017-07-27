<?php


namespace Akademiano\Entity;


class Uuid implements UuidInterface
{
    protected $value;

    /**
     * Uuid constructor.
     * @param $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @param null $value
     */
    public function setValue($value)
    {
        $this->value = (integer) $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getHex()
    {
        return dechex($this->getValue());
    }

    public function toInt()
    {
        return (integer) $this->value;
    }


    public function __toString()
    {
        return (string) $this->getValue();
    }

}

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
        $this->value = (integer)$value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getHex(): string
    {
        return dechex($this->getValue());
    }

    /**
     * @deprecated
     */
    public function getInt(): int
    {
        return $this->value;
    }


    public function __toString()
    {
        return $this->getHex();
    }

    public function serialize()
    {
        return $this->getValue();
    }

    public function unserialize($serialized)
    {
        $this->setValue(unserialize($serialized, ["allowed_classes"=>false]));
    }

    public function jsonSerialize()
    {
        $this->getHex();
    }

    public static function isHexUuid(string $string)
    {
        return !is_numeric($string) && ctype_xdigit($string);
    }

    public static function normalize($value)
    {
        if (is_int($value)) {
            return $value;
        } elseif (is_string($value)) {
            if (self::isHexUuid($value)) {
                return hexdec($value);
            } elseif (is_numeric($value)) {
                return (int)$value;
            } elseif ($value === '') {
                return null;
            } else {
                throw new \InvalidArgumentException(sprintf('Type "%s" value "%s" not supported', gettype($value), json_encode($value, JSON_UNESCAPED_UNICODE)));
            }
        } else {
            throw new \InvalidArgumentException();
        }
    }
}

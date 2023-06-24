<?php


namespace Akademiano\Entity;


class Uuid implements UuidInterface
{
    protected const VALUE_FIELD = "value";

    /** @var int */
    protected $value;

    /**
     * @param $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @param int|string $value
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
        return $this->getValue();
    }


    public function __toString()
    {
        return (string)$this->getHex();
    }

    public function serialize()
    {
        return serialize([
                self::VALUE_FIELD => $this->getValue()
            ]
        );
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized, ["allowed_classes" => false]);
        $this->setValue($data[self::VALUE_FIELD]);
    }

    public function jsonSerialize()
    {
        return [
            self::VALUE_FIELD => $this->getHex(),
        ];
    }

    public static function isHexUuid(string $string): bool
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

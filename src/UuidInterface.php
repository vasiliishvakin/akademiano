<?php

namespace Akademiano\Entity;


use Akademiano\Utils\Object\Prototype\IntegerableInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;

interface UuidInterface extends StringableInterface, IntegerableInterface
{
    /**
     * @return integer
     */
    public function getValue();

    /**
     * @return string
     */
    public function getHex();

    public function getInt();
}

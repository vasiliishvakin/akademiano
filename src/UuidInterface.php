<?php

namespace Akademiano\Entity;


use Akademiano\Utils\Object\Prototype\StringableInterface;

interface UuidInterface extends StringableInterface
{
    /**
     * @return integer
     */
    public function getValue();

    /**
     * @return string
     */
    public function getHex();
}

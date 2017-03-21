<?php

namespace Akademiano\Entity;


use Akademiano\Utils\Object\Prototype\StringableInterface;

interface UuidInterface extends StringableInterface
{

    public function __construct($value = null);


    /**
     * @return integer
     */
    public function getValue();

    /**
     * @return string
     */
    public function getHex();
}

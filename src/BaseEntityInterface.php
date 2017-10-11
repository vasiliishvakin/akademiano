<?php


namespace Akademiano\Entity;


use Akademiano\Utils\Object\Prototype\ArrayableInterface;
use Akademiano\Utils\Object\Prototype\IntegerableInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;

interface BaseEntityInterface extends UuidableInterface, StringableInterface, IntegerableInterface, ArrayableInterface
{
    /**
     * @return UuidInterface
     */
    public function getId();

}

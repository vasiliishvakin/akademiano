<?php


namespace Akademiano\Entity;


use Akademiano\Utils\Object\Prototype\IntegerableInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;

interface BaseEntityInterface extends UuidableInterface, StringableInterface, IntegerableInterface
{
    /**
     * @return UuidInterface
     */
    public function getId();

}

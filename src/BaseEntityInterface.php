<?php


namespace Akademiano\Entity;


use Akademiano\Utils\Object\Prototype\ArrayableInterface;
use Akademiano\Utils\Object\Prototype\IntegerableInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;

interface BaseEntityInterface extends UuidableInterface, StringableInterface, IntegerableInterface, ArrayableInterface, \JsonSerializable
{
    /**
     * @return UuidInterface
     */
    public function getId();

}

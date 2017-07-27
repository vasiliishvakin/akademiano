<?php


namespace Akademiano\Entity;


use Akademiano\Utils\Object\Prototype\IntegerableInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;
use Akademiano\UUID\UuidableInterface;

interface BaseEntityInterface extends UuidableInterface, StringableInterface, IntegerableInterface
{
    /**
     * @return UuidInterface
     */
    public function getId();

}

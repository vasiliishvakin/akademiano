<?php

namespace Akademiano\UUID;


use Akademiano\Entity\UuidInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;

interface UuidComplexInterface extends UuidInterface
{
    public function getDate() :\DateTimeImmutable;

    public function getShard(): int ;

    public function getId(): int ;
}

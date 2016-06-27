<?php


namespace DeltaPhp\Operator\Command;


use DeltaPhp\Operator\Entity\Entity;

interface GenerateIdCommandInterface extends CommandInterface
{
    public function __construct($class = Entity::class);
}

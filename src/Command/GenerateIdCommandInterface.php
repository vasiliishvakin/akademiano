<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\CommandInterface;


use Akademiano\Entity\Entity;

interface GenerateIdCommandInterface extends CommandInterface
{
    public function __construct($class = Entity::class);
}

<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\CommandInterface;
use Akademiano\Entity\Entity;

interface EntityOperatedCommandInterface extends CommandInterface
{
    const DEFAULT_CLASS = Entity::class;
}

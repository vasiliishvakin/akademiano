<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Entity\EntityInterface;
use Akademiano\Operator\Command\CommandInterface;

interface ReserveCommandInterface extends CommandInterface
{
    public function __construct(EntityInterface $entity);
}

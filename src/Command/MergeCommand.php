<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;
use Akademiano\Entity\EntityInterface;

class MergeCommand extends Command
{
    const COMMAND_NAME = "merge";

    public function __construct(EntityInterface $entityA, EntityInterface $entityB, array $params = null)
    {
        $params["entityA"] = $entityA;
        $params["entityB"] = $entityB;
        $classA = get_class($entityA);
        parent::__construct($params, $classA);
    }
}

<?php


namespace DeltaPhp\Operator\Command;

use DeltaPhp\Operator\Entity\EntityInterface;

class CommandMerge extends Command implements CommandInterface
{
    protected $name = self::COMMAND_MERGE;

    public function __construct(EntityInterface $entityA, EntityInterface $entityB, array $params = null)
    {
        $params["entityA"] = $entityA;
        $params["entityB"] = $entityB;
        $classA = get_class($entityA);
        parent::__construct($params, $classA, self::COMMAND_MERGE);
    }
}

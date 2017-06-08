<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;
use Akademiano\Entity\EntityInterface;

class DeleteCommand extends Command
{
    const COMMAND_NAME = "delete";

    public function __construct(EntityInterface $entity, array $params = null)
    {
        $params["entity"] = $entity;
        $class = get_class($entity);
        parent::__construct($params, $class);
    }

}

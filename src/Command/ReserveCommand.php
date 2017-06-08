<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;
use Akademiano\Entity\EntityInterface;

class ReserveCommand extends Command implements ReserveCommandInterface
{
    const COMMAND_NAME = "reserve";

    public function __construct(EntityInterface $entity)
    {
        $params["entity"] = $entity;
        $class = get_class($entity);
        parent::__construct($params, $class);
    }
}

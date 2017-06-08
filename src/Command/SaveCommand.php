<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;
use Akademiano\Entity\EntityInterface;

class SaveCommand extends Command
{
    const COMMAND_NAME = "save";

    public function __construct(EntityInterface $entity = null, $params = [])
    {
        $params["entity"] = $entity;
        $class = get_class($entity);
        parent::__construct($params, $class);
    }
}

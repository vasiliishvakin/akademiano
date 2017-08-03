<?php

namespace Akademiano\EntityOperator\Command;

use Akademiano\Entity\EntityInterface;
use Akademiano\Operator\Command\Command;

class FindRelatedCommand extends Command
{
    const COMMAND_NAME = "find_related";

    public function __construct($class = null, EntityInterface $entity)
    {
        $params["entity"] = $entity;
        parent::__construct($params, $class);
    }
}

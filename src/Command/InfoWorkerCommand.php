<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;
use Akademiano\Entity\Entity;

class InfoWorkerCommand extends Command
{
    const COMMAND_NAME = "info_worker";

    public function __construct($attribute, $class = Entity::class, $params = [])
    {
        $params["attribute"] = $attribute;
        parent::__construct($params, $class);
    }
}

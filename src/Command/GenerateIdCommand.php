<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;

use Akademiano\Entity\Entity;

class GenerateIdCommand extends Command implements GenerateIdCommandInterface
{
    const COMMAND_NAME = "generate.id";

    public function __construct($class = Entity::class)
    {
        $params = [];
        parent::__construct($params, $class);
    }
}

<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;


class CreateCriteriaCommand extends Command
{
    const COMMAND_NAME = "create_criteria";


    public function __construct($class = null, $params = [])
    {
        parent::__construct($params, $class);
    }
}

<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;


class CreateCommand extends Command
{
    const COMMAND_NAME = "create";

    public function __construct($class = null, $params = [])
    {
        parent::__construct($params, $class);
    }
}

<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;


class CountCommand extends Command
{
    const COMMAND_NAME = "count";

    public function __construct(array $params = null, $class = null)
    {
        parent::__construct($params, $class);
    }

}

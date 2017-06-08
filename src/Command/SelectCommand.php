<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;
use Akademiano\Db\Adapter\D2QL\Select;

class SelectCommand extends Command
{
    const COMMAND_NAME = "select";

    public function __construct($class, Select $select, array $params = [])
    {
        $params["select"] = $select;
        parent::__construct($params, $class);
    }
}

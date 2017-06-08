<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;


class FindCommand extends Command
{
    const COMMAND_NAME = "find";

    public function __construct($class = null, $criteria = [], $limit = null, $offset = null, $orderBy = null, $params = [])
    {
        $params["criteria"] = $criteria;
        $params["limit"] = $limit;
        $params["offset"] = $offset;
        $params["orderBy"] = $orderBy;
        parent::__construct($params, $class);
    }

}

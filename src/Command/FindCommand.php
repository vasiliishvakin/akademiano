<?php


namespace DeltaPhp\Operator\Command;


class FindCommand extends Command
{
    protected $name = self::COMMAND_FIND;

    public function __construct($class = null, $criteria = [], $limit = null, $offset = null, $orderBy= null, $params = [])
    {
        $params["criteria"] = $criteria;
        $params["limit"] = $limit;
        $params["offset"] = $offset;
        $params["orderBy"] = $orderBy;
        parent::__construct($params, $class);
    }

}

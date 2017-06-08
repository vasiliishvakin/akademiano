<?php


namespace Akademiano\EntityOperator\Command;

class RelationLoadCommand extends FindCommand
{
    const COMMAND_NAME = "relation.load";

    public function __construct($relationClass, $entity, $criteria = [], $limit = null, $offset = null, $orderBy = null)
    {
        $params["entity"] = $entity;
        parent::__construct($relationClass, $criteria, $limit, $offset, $orderBy, $params);
    }
}

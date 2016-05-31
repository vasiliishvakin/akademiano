<?php


namespace EntityOperator\Command;


class RelationLoadCommand extends FindCommand
{
    const COMMAND_RELATION_LOAD = "relation.load";

    public function __construct($relationClass, $entity, $criteria = [], $limit = null, $offset = null, $orderBy= null)
    {
        $params["entity"] = $entity;
        parent::__construct($relationClass, $criteria, $limit, $offset, $orderBy, $params);
        $this->setName(self::COMMAND_RELATION_LOAD);
    }
}

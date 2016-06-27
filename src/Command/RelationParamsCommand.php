<?php


namespace DeltaPhp\Operator\Command;


use DeltaPhp\Operator\Entity\RelationEntity;

class RelationParamsCommand extends Command implements CommandInterface
{
    const COMMAND_RELATION_PARAMS = "relation.params";

    public function __construct($name, $class = RelationEntity::class, array $params = [])
    {
        $params["param"] = $name;
        parent::__construct($params, $class, self::COMMAND_RELATION_PARAMS);
    }
}

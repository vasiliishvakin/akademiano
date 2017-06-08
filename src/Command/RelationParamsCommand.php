<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;


use DeltaPhp\Operator\Entity\RelationEntity;

class RelationParamsCommand extends Command
{
    const COMMAND_NAME = "relation.params";

    public function __construct($name, $class = RelationEntity::class, array $params = [])
    {
        $params["param"] = $name;
        parent::__construct($params, $class);
    }
}

<?php


namespace DeltaPhp\Operator\Command;


use DeltaPhp\Operator\Entity\Entity;

class GetCommand extends Command
{
    public function __construct($id, $class = Entity::class, $params = [])
    {
        $params["id"] = $id;
        parent::__construct($params, $class, self::COMMAND_GET);
    }

}

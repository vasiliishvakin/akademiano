<?php


namespace Akademiano\Delegating\Command;

use Akademiano\Entity\Entity;

class GetCommand extends Command
{
    const COMMAND_NAME = "get";

    public function __construct($id, $class = Entity::class, $params = [])
    {
        $params["id"] = $id;
        parent::__construct($params, $class);
    }

}

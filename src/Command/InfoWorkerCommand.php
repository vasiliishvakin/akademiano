<?php


namespace DeltaPhp\Operator\Command;


use DeltaPhp\Operator\Entity\Entity;

class InfoWorkerCommand extends Command
{
    public function __construct($attribute, $class = Entity::class, $params = [])
    {
        $params["attribute"] = $attribute;
        parent::__construct($params, $class, self::COMMAND_WORKER_INFO);
    }
}

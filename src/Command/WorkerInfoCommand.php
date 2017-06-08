<?php


namespace Akademiano\Operator\Command;


class WorkerInfoCommand extends Command
{
    public function __construct($attribute, $class, $params = [])
    {
        $params["attribute"] = $attribute;
        parent::__construct($params, $class, self::COMMAND_WORKER_INFO);
    }
}

<?php


namespace DeltaPhp\Operator\Command;


class CreateCriteriaCommand extends Command
{
    const COMMAND_CREATE_CRITERIA = "create_criteria";

    protected $name = self::COMMAND_CREATE_CRITERIA;

    public function __construct($class = null, $params = [])
    {
        parent::__construct($params, $class, self::COMMAND_CREATE);
    }
}

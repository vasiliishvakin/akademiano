<?php


namespace EntityOperator\Command;


class GetCommand extends Command
{
    protected $name = self::COMMAND_GET;

    public function __construct(array $params = null, $class = null)
    {
        parent::__construct($params, $class);
    }

}

<?php


namespace EntityOperator\Command;


class DeleteCommand extends Command
{
    protected $name = self::COMMAND_DELETE;

    public function __construct(array $params = null, $class = null)
    {
        parent::__construct($params, $class);
    }

}

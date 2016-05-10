<?php


namespace EntityOperator\Command;


class FindCommand extends Command
{
    protected $name = self::COMMAND_FIND;

    public function __construct(array $params = null, $class = null)
    {
        parent::__construct($params, $class);
    }

}

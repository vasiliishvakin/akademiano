<?php


namespace EntityOperator\Command;


class SaveCommand extends Command
{
    protected $name = self::COMMAND_SAVE;

    public function __construct(array $params = null, $class = null)
    {
        parent::__construct($params, $class);
    }

}

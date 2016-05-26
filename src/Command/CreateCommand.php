<?php


namespace EntityOperator\Command;


class CreateCommand extends Command implements CommandInterface
{
    protected $name = self::COMMAND_FIND;

    public function __construct($class = null)
    {
        parent::__construct(null, $class, self::COMMAND_CREATE);
    }
}

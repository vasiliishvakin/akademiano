<?php


namespace DeltaPhp\Operator\Command;


class CreateCommand extends Command implements CommandInterface
{
    protected $name = self::COMMAND_FIND;

    public function __construct($class = null, $params = [])
    {
        parent::__construct($params, $class, self::COMMAND_CREATE);
    }
}

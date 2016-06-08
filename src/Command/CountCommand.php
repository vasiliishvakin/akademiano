<?php


namespace DeltaPhp\Operator\Command;


class CountCommand extends Command
{
    protected $name = self::COMMAND_COUNT;

    public function __construct(array $params = null, $class = null)
    {
        parent::__construct($params, $class);
    }

}

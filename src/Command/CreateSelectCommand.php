<?php


namespace DeltaPhp\Operator\Command;


class CreateSelectCommand extends Command implements CommandInterface
{
    const COMMAND_CREATE_SELECT = "create.select";
    protected $name = self::COMMAND_CREATE_SELECT;

    public function __construct($class)
    {
        $this->class = $class;
    }
}

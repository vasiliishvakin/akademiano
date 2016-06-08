<?php


namespace DeltaPhp\Operator\Command;


class GenerateIdCommand extends Command implements GenerateIdCommandInterface
{
    public function __construct($class = Entity::class)
    {
        $params = [];
        parent::__construct($params, $class, self::COMMAND_GENERATE_ID);
    }
}

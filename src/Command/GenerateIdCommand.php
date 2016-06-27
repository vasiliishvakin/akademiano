<?php


namespace DeltaPhp\Operator\Command;

use DeltaPhp\Operator\Entity\Entity;

class GenerateIdCommand extends Command implements GenerateIdCommandInterface
{
    public function __construct($class = Entity::class)
    {
        $params = [];
        parent::__construct($params, $class, self::COMMAND_GENERATE_ID);
    }
}

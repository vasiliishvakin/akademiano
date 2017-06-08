<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;


class CreateSelectCommand extends Command
{
    const COMMAND_NAME = "create.select";

    public function __construct($class)
    {
        parent::__construct([], $class);
    }
}

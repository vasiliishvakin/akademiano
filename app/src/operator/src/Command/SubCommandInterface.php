<?php


namespace Akademiano\Operator\Command;

use \Akademiano\Delegating\Command\CommandInterface;

interface SubCommandInterface extends CommandInterface
{
    const PARAM_PARENT_COMMAND = 'parentCommand';

    public function getParentCommand():CommandInterface;
}

<?php


namespace Akademiano\Operator\Command;

use \Akademiano\Delegating\Command\CommandInterface;

interface SubCommandInterface extends CommandInterface
{
    public function getPrefix();
}

<?php


namespace Akademiano\Delegating;

use Akademiano\Delegating\Command\CommandInterface;

interface OperatorInterface
{
    public function execute(CommandInterface $command);
}

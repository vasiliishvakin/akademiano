<?php


namespace Akademiano\Delegating;


use Akademiano\Delegating\Command\CommandInterface;

interface DelegatingInterface extends IncludeOperatorInterface
{
    public function delegate(CommandInterface $command);
}

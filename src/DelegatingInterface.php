<?php


namespace Akademiano\Operator;


use Akademiano\Operator\Command\CommandInterface;

interface DelegatingInterface extends IncludeOperatorInterface
{
    public function delegate(CommandInterface $command);
}

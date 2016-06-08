<?php


namespace DeltaPhp\Operator;


use DeltaPhp\Operator\Command\CommandInterface;

interface DelegatingInterface extends IncludeOperatorInterface
{
    public function delegate(CommandInterface $command);
}

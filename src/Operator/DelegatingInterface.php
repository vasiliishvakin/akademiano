<?php


namespace EntityOperator\Operator;


use EntityOperator\Command\CommandInterface;

interface DelegatingInterface extends IncludeOperatorInterface
{
    public function delegate(CommandInterface $command);
}

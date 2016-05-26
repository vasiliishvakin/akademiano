<?php


namespace EntityOperator\Worker;


use EntityOperator\Command\CommandInterface;
use EntityOperator\Operator\OperatorInterface;

interface DelegatingWorkerInterface
{
    public function setOperator(OperatorInterface $operator);

    public function delegate(CommandInterface $command);

}
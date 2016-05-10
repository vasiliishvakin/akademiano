<?php


namespace EntityOperator\Operator;

use EntityOperator\Worker\WorkerInterface;
use \EntityOperator\Command\CommandInterface;

interface OperatorInterface
{
    public function execute(CommandInterface $command);

}
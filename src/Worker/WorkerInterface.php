<?php


namespace EntityOperator\Worker;


use EntityOperator\Command\CommandInterface;

interface WorkerInterface
{

    public function execute(CommandInterface $command);

}
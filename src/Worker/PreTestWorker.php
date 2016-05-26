<?php


namespace EntityOperator\Worker;


use EntityOperator\Command\CommandInterface;

class PreTestWorker implements WorkerInterface
{

    public function execute(CommandInterface $command)
    {
        return [123];
    }
}
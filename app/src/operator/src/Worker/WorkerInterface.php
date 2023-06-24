<?php


namespace Akademiano\Operator\Worker;


use Akademiano\Delegating\Command\CommandInterface;

interface WorkerInterface
{
    public function execute(CommandInterface $command);
}

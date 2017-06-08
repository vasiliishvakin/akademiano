<?php


namespace Akademiano\Operator;

use Akademiano\Operator\Command\CommandInterface;

interface OperatorInterface
{
    public function execute(CommandInterface $command);

    public function addWorker($name, Callable $worker);

    public function addAction($action, $workerName, $class = null);

    public function getDependency($name);
}

<?php


namespace Akademiano\Operator;

interface OperatorInterface extends \Akademiano\Delegating\OperatorInterface
{
    public function addWorker($name, Callable $worker);

    public function addAction($action, $workerName, $class = null);

    public function getDependency($name);
}

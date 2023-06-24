<?php


namespace Akademiano\Operator\Worker;


use Akademiano\Operator\WorkersContainer;

trait WorkerSelfInstanceTrait
{
    public static function getSelfInstance(WorkersContainer $container):WorkerInterface
    {
        return new static($container->getOperator());
    }
}

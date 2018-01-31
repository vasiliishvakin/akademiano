<?php


namespace Akademiano\Operator\Worker;


use Akademiano\Operator\WorkersContainer;

interface WorkerSelfInstancedInterface
{
    const SELF_INSTANCE_COMMAND_NAME = 'getSelfInstance';

    public static function getSelfInstance(WorkersContainer $container): WorkerInterface;
}

<?php

use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    "groupsWorker" => [
        \Akademiano\UserEO\Model\GroupsWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\UserEO\Model\Group::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\UserEO\Model\GroupsWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
    "usersWorker" => [
        \Akademiano\UserEO\Model\UsersWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\UserEO\Model\User::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\UserEO\Model\UsersWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
];

<?php

use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    "entityFileRelationWorker" => [
        \Akademiano\Attach\Model\EntityFileRelation::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Attach\Model\EntityFileRelation::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Attach\Model\EntityFileRelationWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
];

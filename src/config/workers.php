<?php

use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    \Akademiano\Attach\Model\LinkedFilesWorker::WORKER_NAME => [
        \Akademiano\Attach\Model\LinkedFilesWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Attach\Model\LinkedFile::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Attach\Model\LinkedFilesWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
    \Akademiano\Attach\Model\RelatedFilesWorker::WORKER_NAME => [
        \Akademiano\Attach\Model\RelatedFilesWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Attach\Model\RelatedFile::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Attach\Model\RelatedFilesWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
    \Akademiano\Attach\Model\EntityFileRelationWorker::WORKER_NAME => [
        \Akademiano\Attach\Model\EntityFileRelationWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Attach\Model\EntityFileRelation::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Attach\Model\EntityFileRelationWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
];

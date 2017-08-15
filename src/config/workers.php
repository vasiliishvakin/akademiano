<?php

use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    "filesWorker" => [
        \Akademiano\Content\Files\Model\FilesWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Content\Files\Model\FilesWorker::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Content\Files\Model\FilesWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
];

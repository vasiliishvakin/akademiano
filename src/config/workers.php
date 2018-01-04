<?php

use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    "filesWorker" => [
        \Akademiano\Content\Files\Model\FilesWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Content\Files\Model\File::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Content\Files\Model\FilesWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],

    \Akademiano\Content\Files\Model\MimeyExtensionWorker::WORKER_NAME => [
        \Akademiano\Content\Files\Model\MimeyExtensionWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Content\Files\Model\File::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Content\Files\Model\MimeyExtensionWorker();
            $mimey = $s->getOperator()->getDependency("mimey");
            $w->setMimey($mimey);
            return $w;
        },
    ]
];

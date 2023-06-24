<?php

use Akademiano\EntityOperator\Worker\EntityWorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    /*"filesWorker" => [
        \Akademiano\Content\Files\Model\FilesWorker::class,
        EntityWorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Content\Files\Model\File::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Content\Files\Model\FilesWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],*/

    \Akademiano\Content\Files\Model\MimeyExtensionWorker::class,
];

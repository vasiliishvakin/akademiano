<?php
use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    "entriesWorker" => [
        \Akademiano\Content\Entries\Model\EntriesWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Content\Entries\Model\Entry::class,
        function (WorkersContainerInterface $s) {
            $w = new  \Akademiano\Content\Entries\Model\EntriesWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
];

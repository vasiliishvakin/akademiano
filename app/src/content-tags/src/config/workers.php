<?php

use Akademiano\EntityOperator\Worker\EntityWorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    \Akademiano\Content\Tags\Model\DictionariesWorker::class,
    \Akademiano\Content\Tags\Model\TagsWorker::class,
    \Akademiano\Content\Tags\Model\TagsRelationsWorker::class,
    \Akademiano\Content\Tags\Model\TagsDictionariesRelationWorker::class,
];

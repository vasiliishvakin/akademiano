<?php

use Akademiano\EntityOperator\Worker\EntityWorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    \Akademiano\Content\Articles\Model\ArticlesWorker::class,
    \Akademiano\Content\Articles\Model\ArticleFilesWorker::class,
];

<?php

use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    "articlesWorker" => [
        \Akademiano\Content\Articles\Model\ArticleWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Content\Articles\Model\Article::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Content\Articles\Model\ArticleWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
];

<?php

use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    "articlesWorker" => [
        \Akademiano\Content\Articles\Model\ArticlesWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Content\Articles\Model\Article::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Content\Articles\Model\ArticlesWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
    "articleFilesWorker" => [
        \Akademiano\Content\Articles\Model\ArticleFilesWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Content\Articles\Model\ArticleFile::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Content\Articles\Model\ArticleFilesWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
];

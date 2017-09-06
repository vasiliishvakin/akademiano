<?php

use Akademiano\Operator\WorkersContainerInterface;

return [
    "CommentsWorker" => [
        \Akademiano\Content\Comments\Model\CommentsWorker::class,
        \Akademiano\EntityOperator\Worker\WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Content\Comments\Model\Comment::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Content\Comments\Model\CommentsWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
    \Akademiano\Content\Comments\Model\CommentFilesWorker::WORKER_NAME => [
        \Akademiano\Content\Comments\Model\CommentFilesWorker::class,
        \Akademiano\EntityOperator\Worker\WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Content\Comments\Model\CommentFile::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Content\Comments\Model\CommentFilesWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
];

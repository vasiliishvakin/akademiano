<?php

use Akademiano\Operator\WorkersContainerInterface;

return [
    "CommentsWorker" => [
        \Akademiano\Content\Comments\Model\CommentsWorker::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Content\Comments\Model\CommentsWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
];

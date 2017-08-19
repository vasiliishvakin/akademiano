<?php

use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    "messagesWorker" => [
        \Akademiano\Messages\Model\MessagesWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Messages\Model\Message::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Messages\Model\MessagesWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
    "parseMessagesWorker" => [
        \Akademiano\Messages\Model\ParseMessageWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Messages\Model\Message::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Messages\Model\ParseMessageWorker();
            $view = $s->getOperator()->getDependency("view");
            $w->setView($view);
            return $w;
        },
    ],
];

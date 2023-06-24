<?php

use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    "heraldMessagesWorker" => [
        \Akademiano\HeraldMessages\Model\MessagesWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\HeraldMessages\Model\Message::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\HeraldMessages\Model\MessagesWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
    "heraldMessageEmailWorker" => [
        \Akademiano\HeraldMessages\Model\SendMessageEmailWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\HeraldMessages\Model\Message::class,
        function (WorkersContainerInterface $s) {
            /** @var \Akademiano\Config\Config $config */
            $config = $s->getOperator()->getDependency("config");
            $w = new \Akademiano\HeraldMessages\Model\SendMessageEmailWorker();
            $w->setMailer($s->getOperator()->getDependency("mailer"));
            $from = $config->getOrThrow(["email", "smtp", "from"]);
            $from = ($from instanceof \Akademiano\Config\Config) ? $from->toArray() : $from;
            $w->setFrom($from);
            return $w;
        },
    ],
    \Akademiano\HeraldMessages\Model\RegisterMessageWorker::WORKER_NAME => [
        \Akademiano\HeraldMessages\Model\RegisterMessageWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\HeraldMessages\Model\Message::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\HeraldMessages\Model\RegisterMessageWorker();
            return $w;
        },
    ],
];

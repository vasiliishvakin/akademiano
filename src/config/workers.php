<?php

return [
    \Akademiano\Messages\Model\MessagesWorker::class,
    \Akademiano\Messages\Model\ParseMessageWorker::class,
    \Akademiano\Messages\Model\SendMessageEmailWorker::class,
//    "parseMessagesWorker" => [
//        \Akademiano\Messages\Model\ParseMessageWorker::class,
//        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Messages\Model\Message::class,
//        function (WorkersContainerInterface $s) {
//            $w = new \Akademiano\Messages\Model\ParseMessageWorker();
//            $view = $s->getOperator()->getDependency("view");
//            $w->setView($view);
//            return $w;
//        },
//    ],
//    "sendMessageEmailWorker" => [
//        \Akademiano\Messages\Model\SendMessageEmailWorker::class,
//        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Messages\Model\Message::class,
//        function (WorkersContainerInterface $s) {
//            /** @var \Akademiano\Config\Config $config */
//            $config = $s->getOperator()->getDependency("config");
//            $w = new \Akademiano\Messages\Model\SendMessageEmailWorker();
//            $w->setMailer($s->getOperator()->getDependency("mailer"));
//            $from = $config->getOrThrow(["email", "smtp", "from"]);
//            $from = ($from instanceof \Akademiano\Config\Config) ? $from->toArray() : $from;
//            $w->setFrom($from);
//            return $w;
//        },
//    ],
];

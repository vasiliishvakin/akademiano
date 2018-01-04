<?php

use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    \Akademiano\Content\Files\Images\Model\ImageFormatWorker::WORKER_NAME => [
        \Akademiano\Content\Files\Images\Model\ImageFormatWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Content\Files\Model\File::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\Content\Files\Images\Model\ImageFormatWorker();
            $operator = $s->getOperator();
            $imageProcessor = $operator->getDependency("imageProcessor");
            $w->setImageProcessor($imageProcessor);

            /** @var \Akademiano\Config\Config $config */
            $config = $operator->getDependency("config");
            $templates = $config->get(['content', 'files', 'image', 'templates'], [])->toArray();
            $w->setTemplates($templates);
            return $w;
        },
    ]
];

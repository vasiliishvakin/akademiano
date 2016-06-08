<?php
use EntityOperator\Worker\WorkerInterface;
use UUID\Model\Command\CreateUuidCommand;

return [
    "UuidWorker" => [
        function ($s) {
            $w = new \UUID\Model\Worker\UuidWorker();
            $uf = $s->getOperator()->getDependency("uuidFactory");
            $w->setUuidFactory($uf);
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            \EntityOperator\Command\CommandInterface::COMMAND_CREATE => \UUID\Model\UuidComplexShortTables::class,
            CreateUuidCommand::COMMAND_UUID_CREATE => null,
        ],
    ]
];

<?php
use DeltaPhp\Operator\Worker\WorkerInterface;
use DeltaPhp\Operator\Command\CommandInterface;
use UUID\Model\Command\CreateUuidCommand;
use UUID\Model\UuidComplexShortTables;

return [
    "UuidWorker" => [
        function ($s) {
            $w = new \UUID\Model\Worker\UuidWorker();
            $uf = $s->getOperator()->getDependency("uuidFactory");
            $w->setUuidFactory($uf);
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_CREATE => UuidComplexShortTables::class,
            CreateUuidCommand::COMMAND_UUID_CREATE => null,
        ],
    ]
];

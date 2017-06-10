<?php
use Akademiano\EntityOperator\Worker\WorkerInterface;
use \Akademiano\EntityOperator\Command\CreateCommand;
use Akademiano\UUID\Command\CreateUuidCommand;
use Akademiano\UUID\UuidComplexShortTables;

return [
    "UuidWorker" => [
        function (\Akademiano\Operator\WorkersContainerInterface $s) {
            $w = new \Akademiano\UUID\Worker\UuidWorker();
            $uf = $s->getOperator()->getDependency("uuidFactory");
            $w->setUuidFactory($uf);
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CreateCommand::COMMAND_NAME => UuidComplexShortTables::class,
            CreateUuidCommand::COMMAND_NAME => null,
        ],
    ]
];

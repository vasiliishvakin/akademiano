<?php
use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Entity\NamedEntity;
use Akademiano\Entity\ContentEntity;
use Akademiano\Operator\Command\AfterCommandInterface;
use Akademiano\Operator\WorkersContainerInterface;
use Akademiano\Operator\Command\PreCommandInterface;
use Akademiano\EntityOperator\Entity\RelationEntity;
use Akademiano\EntityOperator\Command\CreateSelectCommand;
use Akademiano\EntityOperator\Command\SelectCommand;

use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\EntityOperator\Command\CountCommand;
use Akademiano\EntityOperator\Command\SaveCommand;
use Akademiano\EntityOperator\Command\DeleteCommand;
use Akademiano\EntityOperator\Command\LoadCommand;
use Akademiano\EntityOperator\Command\ReserveCommand;
use Akademiano\EntityOperator\Command\GenerateIdCommand;
use Akademiano\Operator\Command\WorkerInfoCommand;

//PostgresWorker
return [
    "EntityCreatorWorker" => [
        \Akademiano\EntityOperator\Worker\EntityCreatorWorker::class,
        function ($s) {
            $w = new \Akademiano\EntityOperator\Worker\EntityCreatorWorker();
            return $w;
        },
    ],

    "EntityWorker" => [
        \Akademiano\EntityOperator\Worker\PostgresWorker::class,
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\EntityOperator\Worker\PostgresWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 1,
    ],

    "NamedEntitiesWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\EntityOperator\Worker\PostgresWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            $w->setTable("named");
            $w->addFields(["title", "description"]);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 2,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            FindCommand::COMMAND_NAME => NamedEntity::class,
            PreCommandInterface::PREFIX_COMMAND_PRE . FindCommand::COMMAND_NAME => NamedEntity::class,
            GetCommand::COMMAND_NAME => NamedEntity::class,
            CountCommand::COMMAND_NAME => NamedEntity::class,
            SaveCommand::COMMAND_NAME => NamedEntity::class,
            DeleteCommand::COMMAND_NAME => NamedEntity::class,
            LoadCommand::COMMAND_NAME => NamedEntity::class,
            ReserveCommand::COMMAND_NAME => NamedEntity::class,
            GenerateIdCommand::COMMAND_NAME => NamedEntity::class,
            CreateSelectCommand::COMMAND_NAME => NamedEntity::class,
            SelectCommand::COMMAND_NAME => NamedEntity::class,
        ],
    ],


    "ContentEntitiesWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\EntityOperator\Worker\PostgresWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            $w->setTable("content");
            $w->addFields(["title", "description", "content"]);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 3,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            FindCommand::COMMAND_NAME => ContentEntity::class,
            PreCommandInterface::PREFIX_COMMAND_PRE . FindCommand::COMMAND_NAME => ContentEntity::class,
            GetCommand::COMMAND_NAME => ContentEntity::class,
            CountCommand::COMMAND_NAME => ContentEntity::class,
            SaveCommand::COMMAND_NAME => ContentEntity::class,
            DeleteCommand::COMMAND_NAME => ContentEntity::class,
            LoadCommand::COMMAND_NAME => ContentEntity::class,
            ReserveCommand::COMMAND_NAME => ContentEntity::class,
            GenerateIdCommand::COMMAND_NAME => ContentEntity::class,
            CreateSelectCommand::COMMAND_NAME => ContentEntity::class,
            SelectCommand::COMMAND_NAME => ContentEntity::class,
        ],
    ],

    "RelationEntitiesWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\EntityOperator\Worker\PostgresWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            $w->setTable("relations");
            $w->addFields(["first", "second"]);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 4,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            FindCommand::COMMAND_NAME => RelationEntity::class,
            PreCommandInterface::PREFIX_COMMAND_PRE . FindCommand::COMMAND_NAME => RelationEntity::class,
            GetCommand::COMMAND_NAME => RelationEntity::class,
            CountCommand::COMMAND_NAME => RelationEntity::class,
            SaveCommand::COMMAND_NAME => RelationEntity::class,
            DeleteCommand::COMMAND_NAME => RelationEntity::class,
            LoadCommand::COMMAND_NAME => RelationEntity::class,
            ReserveCommand::COMMAND_NAME => RelationEntity::class,
            WorkerInfoCommand::COMMAND_NAME => RelationEntity::class,
            GenerateIdCommand::COMMAND_NAME => RelationEntity::class,
            CreateSelectCommand::COMMAND_NAME => RelationEntity::class,
            SelectCommand::COMMAND_NAME => RelationEntity::class,
        ],
    ],

    "TranslatorDataToObjectWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\EntityOperator\Worker\TranslatorDataToObjectWorker();
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            AfterCommandInterface::PREFIX_COMMAND_AFTER . FindCommand::COMMAND_NAME => null,
            AfterCommandInterface::PREFIX_COMMAND_AFTER . GetCommand::COMMAND_NAME => null,
        ],
    ],

    "SetEntityExistedWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\EntityOperator\Worker\SetEntityExistingWorker();
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            AfterCommandInterface::PREFIX_COMMAND_AFTER . FindCommand::COMMAND_NAME => [null => 5],
            AfterCommandInterface::PREFIX_COMMAND_AFTER . GetCommand::COMMAND_NAME => [null => 5],
        ],
    ],

    "TranslatorObjectToDataWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \Akademiano\EntityOperator\Worker\TranslatorObjectToDataWorker();
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            \Akademiano\EntityOperator\Worker\TranslatorObjectToDataWorker::COMMAND_BEFORE_SAVE => null,
            \Akademiano\EntityOperator\Worker\TranslatorObjectToDataWorker::COMMAND_BEFORE_DELETE => null,
        ],
    ],
];

<?php
use DeltaPhp\Operator\Worker\WorkerInterface;
use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Entity\NamedEntity;
use DeltaPhp\Operator\Entity\ContentEntity;
use DeltaPhp\Operator\Command\AfterCommandInterface;
use DeltaPhp\Operator\WorkersContainerInterface;
use DeltaPhp\Operator\Command\PreCommandInterface;
use DeltaPhp\Operator\Entity\RelationEntity;
use DeltaPhp\Operator\Command\CreateSelectCommand;
use DeltaPhp\Operator\Command\SelectCommand;

//PostgresWorker
return [
    "EntityCreatorWorker" => [
        \DeltaPhp\Operator\Worker\EntityCreatorWorker::class,
        function ($s) {
            $w = new \DeltaPhp\Operator\Worker\EntityCreatorWorker();
            return $w;
        },
    ],

    "EntityWorker" => [
        \DeltaPhp\Operator\Worker\PostgresWorker::class,
        function (WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\PostgresWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 1,
    ],

    "NamedEntitiesWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\PostgresWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            $w->setTable("named");
            $w->addFields(["title", "description"]);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 2,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_FIND => NamedEntity::class,
            PreCommandInterface::PREFIX_COMMAND_PRE . CommandInterface::COMMAND_FIND => NamedEntity::class,
            CommandInterface::COMMAND_GET => NamedEntity::class,
            CommandInterface::COMMAND_COUNT => NamedEntity::class,
            CommandInterface::COMMAND_SAVE => NamedEntity::class,
            CommandInterface::COMMAND_DELETE => NamedEntity::class,
            CommandInterface::COMMAND_LOAD => NamedEntity::class,
            CommandInterface::COMMAND_RESERVE => NamedEntity::class,
            CommandInterface::COMMAND_GENERATE_ID => NamedEntity::class,
            CommandInterface::COMMAND_WORKER_INFO => NamedEntity::class,
            CreateSelectCommand::COMMAND_CREATE_SELECT => NamedEntity::class,
            SelectCommand::COMMAND_SELECT => NamedEntity::class,
        ],
    ],


    "ContentEntitiesWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\PostgresWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            $w->setTable("content");
            $w->addFields(["title", "description", "content"]);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 3,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_FIND => ContentEntity::class,
            PreCommandInterface::PREFIX_COMMAND_PRE . CommandInterface::COMMAND_FIND => ContentEntity::class,
            CommandInterface::COMMAND_GET => ContentEntity::class,
            CommandInterface::COMMAND_COUNT => ContentEntity::class,
            CommandInterface::COMMAND_SAVE => ContentEntity::class,
            CommandInterface::COMMAND_DELETE => ContentEntity::class,
            CommandInterface::COMMAND_LOAD => ContentEntity::class,
            CommandInterface::COMMAND_RESERVE => ContentEntity::class,
            CommandInterface::COMMAND_GENERATE_ID => ContentEntity::class,
            CommandInterface::COMMAND_WORKER_INFO => ContentEntity::class,
            CreateSelectCommand::COMMAND_CREATE_SELECT => ContentEntity::class,
            SelectCommand::COMMAND_SELECT => ContentEntity::class,
        ],
    ],

    "RelationEntitiesWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\PostgresWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            $w->setTable("relations");
            $w->addFields(["first", "second"]);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 4,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_FIND => RelationEntity::class,
            PreCommandInterface::PREFIX_COMMAND_PRE . CommandInterface::COMMAND_FIND => RelationEntity::class,
            CommandInterface::COMMAND_GET => RelationEntity::class,
            CommandInterface::COMMAND_COUNT => RelationEntity::class,
            CommandInterface::COMMAND_SAVE => RelationEntity::class,
            CommandInterface::COMMAND_DELETE => RelationEntity::class,
            CommandInterface::COMMAND_LOAD => RelationEntity::class,
            CommandInterface::COMMAND_RESERVE => RelationEntity::class,
            CommandInterface::COMMAND_GENERATE_ID => RelationEntity::class,
            CommandInterface::COMMAND_WORKER_INFO => RelationEntity::class,
            CreateSelectCommand::COMMAND_CREATE_SELECT => RelationEntity::class,
            SelectCommand::COMMAND_SELECT => RelationEntity::class,
        ],
    ],

    "TranslatorDataToObjectWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\TranslatorDataToObjectWorker();
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            AfterCommandInterface::COMMAND_AFTER_FIND => null,
            AfterCommandInterface::COMMAND_AFTER_GET => null,
        ],
    ],

    "SetEntityExistedWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\SetEntityExistingWorker();
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            AfterCommandInterface::COMMAND_AFTER_FIND => [null => 5],
            AfterCommandInterface::COMMAND_AFTER_GET => [null => 5],
        ],
    ],

    "TranslatorObjectToDataWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\TranslatorObjectToDataWorker();
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            \DeltaPhp\Operator\Worker\TranslatorObjectToDataWorker::COMMAND_BEFORE_SAVE => null,
            \DeltaPhp\Operator\Worker\TranslatorObjectToDataWorker::COMMAND_BEFORE_DELETE => null,
        ],
    ],

    "IntIdToUuidObjectWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\IntIdToUuidObjectWorker();
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            \DeltaPhp\Operator\Worker\IntIdToUuidObjectWorker::COMMAND_AFTER_GENERATE_ID => null,
        ],
    ],
];

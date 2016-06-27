<?php
use DeltaPhp\Operator\Worker\WorkerInterface;
use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Entity\NamedEntity;
use DeltaPhp\Operator\Entity\TextEntity;
use DeltaPhp\Operator\Command\AfterCommandInterface;
use DeltaPhp\Operator\WorkersContainerInterface;
use \DeltaPhp\Operator\Command\PreCommandInterface;

//PostgresWorker
return [
    "EntityCreatorWorker" => [
        function ($s) {
            $w = new \DeltaPhp\Operator\Worker\EntityCreatorWorker();
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_CREATE => null,
        ],
    ],

    "EntityWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\PostgresWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 1,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_FIND => null,
            PreCommandInterface::PREFIX_COMMAND_PRE . CommandInterface::COMMAND_FIND => null,
            CommandInterface::COMMAND_GET => null,
            CommandInterface::COMMAND_COUNT => null,
            CommandInterface::COMMAND_SAVE => null,
            CommandInterface::COMMAND_DELETE => null,
            CommandInterface::COMMAND_LOAD => null,
            CommandInterface::COMMAND_RESERVE => null,
            CommandInterface::COMMAND_GENERATE_ID => null,
        ],
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
            CommandInterface::COMMAND_GET => NamedEntity::class,
            CommandInterface::COMMAND_COUNT => NamedEntity::class,
            CommandInterface::COMMAND_SAVE => NamedEntity::class,
            CommandInterface::COMMAND_DELETE => NamedEntity::class,
            CommandInterface::COMMAND_LOAD => NamedEntity::class,
            CommandInterface::COMMAND_RESERVE => NamedEntity::class,
            CommandInterface::COMMAND_GENERATE_ID => NamedEntity::class,
        ],
    ],

    "TextEntitiesWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\PostgresWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            $w->setTable("texts");
            $w->addField("content");
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 3,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_FIND => TextEntity::class,
            CommandInterface::COMMAND_GET => TextEntity::class,
            CommandInterface::COMMAND_COUNT => TextEntity::class,
            CommandInterface::COMMAND_SAVE => TextEntity::class,
            CommandInterface::COMMAND_DELETE => TextEntity::class,
            CommandInterface::COMMAND_LOAD => TextEntity::class,
            CommandInterface::COMMAND_RESERVE => TextEntity::class,
            CommandInterface::COMMAND_GENERATE_ID => TextEntity::class,
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
        function(WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\IntIdToUuidObjectWorker();
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            \DeltaPhp\Operator\Worker\IntIdToUuidObjectWorker::COMMAND_AFTER_GENERATE_ID => null,
        ],
    ]
];

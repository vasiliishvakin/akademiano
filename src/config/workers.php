<?php
use EntityOperator\Worker\WorkerInterface;
use EntityOperator\Command\CommandInterface;
use EntityOperator\Entity\TextEntity;
use \EntityOperator\Command\PreCommandInterface;

//PostgresWorker
return [
    "EntityCreatorWorker" => [
        function ($s) {
            $w = new \EntityOperator\Worker\EntityCreatorWorker();
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_CREATE => null,
        ],
    ],

    "EntityLoaderWorker" => [
        function ($s) {
            $w = new \EntityOperator\Worker\EntityLoaderWorker();
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_LOAD => null,
        ]
    ],

    "EntityWorker" => [
        function ($s) {
            $w = new \EntityOperator\Worker\PostgresWorker();
            /** @var $o \EntityOperator\EntityOperator */
            $adapter = $s["operator"]->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 1,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_FIND => null,
            CommandInterface::COMMAND_GET => null,
            CommandInterface::COMMAND_COUNT => null,
            CommandInterface::COMMAND_SAVE => null,
            CommandInterface::COMMAND_DELETE => null,
        ],
    ],

    "NamedEntitiesWorker" => [
        function ($s) {
            $w = new \EntityOperator\Worker\PostgresWorker();
            /** @var $o \EntityOperator\EntityOperator */
            $adapter = $s["operator"]->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            $w->setTable("named");
            $w->addFields(["title", "description"]);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 2,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_FIND => null,
            CommandInterface::COMMAND_GET => null,
            CommandInterface::COMMAND_COUNT => null,
            CommandInterface::COMMAND_SAVE => null,
            CommandInterface::COMMAND_DELETE => null,
        ],
    ],

    "TextEntitiesWorker" => [
        function ($s) {
            $w = new \EntityOperator\Worker\PostgresWorker();
            /** @var $o \EntityOperator\EntityOperator */
            $adapter = $s["operator"]->getDependency("dbAdapter");
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
        ],

    ],
    
    "TranslatorDataToObjectWorker" => [
        function($s) {
            $w = new \EntityOperator\Worker\TranslatorDataToObjectWorker();
            $w->setOperator($s["operator"]);
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            \EntityOperator\Worker\TranslatorDataToObjectWorker::COMMAND_AFTER_FIND => null,
        ],
    ]
];

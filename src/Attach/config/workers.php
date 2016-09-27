<?php
use DeltaPhp\Operator\Worker\WorkerInterface;
use DeltaPhp\Operator\Command\CommandInterface;
use Attach\Model\ImageFileEntity;
use Attach\Model\FileEntity;
use DeltaPhp\Operator\Entity\Entity;
use Attach\Model\EntityFileRelation;
use Attach\Model\EntityImageRelation;
use DeltaPhp\Operator\Command\RelationLoadCommand;

return [
    "FileAttachWorker" => [
        function ($s) {
            $w = new \Attach\Model\Worker\FileAttachWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 12,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_FIND => \Attach\Model\FileEntity::class,
            CommandInterface::COMMAND_GET => FileEntity::class,
            CommandInterface::COMMAND_COUNT => FileEntity::class,
            CommandInterface::COMMAND_SAVE => FileEntity::class,
            CommandInterface::COMMAND_DELETE => FileEntity::class,
            CommandInterface::COMMAND_LOAD => FileEntity::class,
            CommandInterface::COMMAND_RESERVE => FileEntity::class,
            CommandInterface::COMMAND_GENERATE_ID => FileEntity::class,

            \Attach\Model\Command\AddFileCommand::COMMAND_ADD_FILE => null,
            \Attach\Model\Command\UpdateFileCommand::COMMAND_UPDATE_FILE => null,
            \Attach\Model\Command\DeleteFileCommand::COMMAND_DELETE_FILE => null,
        ],
    ],
    "ImageAttachWorker" => [
        function ($s) {
            $w = new \Attach\Model\Worker\ImageAttachWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 13,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_FIND => ImageFileEntity::class,
            CommandInterface::COMMAND_GET => ImageFileEntity::class,
            CommandInterface::COMMAND_COUNT => ImageFileEntity::class,
            CommandInterface::COMMAND_SAVE => ImageFileEntity::class,
            CommandInterface::COMMAND_DELETE => ImageFileEntity::class,
            CommandInterface::COMMAND_LOAD => ImageFileEntity::class,
            CommandInterface::COMMAND_RESERVE => ImageFileEntity::class,
            CommandInterface::COMMAND_GENERATE_ID => ImageFileEntity::class,
        ],
    ],
    "ParseRequestFilesWorker" => [
        function ($s) {
            $w = new \Attach\Model\Worker\ParseRequestFilesWorker();
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            \Attach\Model\Command\ParseRequestFilesCommand::COMMAND_PARSE_REQUEST_FILES => null,
        ],
    ],
    "EntityAttachSaveWorker" => [
        function ($s) {
            $w = new \Attach\Model\Worker\EntityAttachSaveWorker();
            $config= $s->getOperator()->getDependency("config")->get("Attach", []);
            $w->addConfig($config);
            return $w;
        },
        WorkerInterface::PARAM_ACTIONS_MAP => [
            \Attach\Model\Command\EntityAttachSaveCommand::COMMAND_ATTACH_SAVE => \DeltaPhp\Operator\Entity\RelationEntity::class
        ],
    ],
    "EntityFilesWorker" => [
        function ($s) {
            $worker = new \DeltaPhp\Operator\Worker\RelationsWorker(Entity::class, FileEntity::class, EntityFileRelation::class, "entity_file_relations");
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $worker->setAdapter($adapter);
            return $worker;
        },
        WorkerInterface::PARAM_TABLEID => 80,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            RelationLoadCommand::COMMAND_RELATION_LOAD => EntityFileRelation::class,
            CommandInterface::COMMAND_FIND => EntityFileRelation::class,
            CommandInterface::COMMAND_LOAD => EntityFileRelation::class,
            CommandInterface::COMMAND_RESERVE => EntityFileRelation::class,
            CommandInterface::COMMAND_GENERATE_ID => EntityFileRelation::class,
            CommandInterface::COMMAND_GET => EntityFileRelation::class,
            CommandInterface::COMMAND_COUNT => EntityFileRelation::class,
            CommandInterface::COMMAND_SAVE => EntityFileRelation::class,
            CommandInterface::COMMAND_DELETE => EntityFileRelation::class,
            \DeltaPhp\Operator\Command\RelationParamsCommand::COMMAND_RELATION_PARAMS => EntityFileRelation::class,
            CommandInterface::COMMAND_WORKER_INFO => EntityFileRelation::class,
        ],
    ],

    "EntityImagesWorker" => [
        function ($s) {
            $worker = new \DeltaPhp\Operator\Worker\RelationsWorker(Entity::class, ImageFileEntity::class, EntityImageRelation::class, "entity_file_relations");
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $worker->setAdapter($adapter);
            return $worker;
        },
        WorkerInterface::PARAM_TABLEID => 81,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            RelationLoadCommand::COMMAND_RELATION_LOAD => EntityImageRelation::class,
            CommandInterface::COMMAND_FIND => EntityImageRelation::class,
            CommandInterface::COMMAND_LOAD => EntityImageRelation::class,
            CommandInterface::COMMAND_RESERVE => EntityImageRelation::class,
            CommandInterface::COMMAND_GENERATE_ID => EntityImageRelation::class,
            CommandInterface::COMMAND_GET => EntityImageRelation::class,
            CommandInterface::COMMAND_COUNT => EntityImageRelation::class,
            CommandInterface::COMMAND_SAVE => EntityImageRelation::class,
            CommandInterface::COMMAND_DELETE => EntityImageRelation::class,
            \DeltaPhp\Operator\Command\RelationParamsCommand::COMMAND_RELATION_PARAMS => EntityImageRelation::class,
            CommandInterface::COMMAND_WORKER_INFO => EntityImageRelation::class,
        ],
    ],
];

<?php
use DeltaPhp\Operator\Worker\WorkerInterface;
use DeltaPhp\Operator\Command\CommandInterface;
use Attach\Model\ImageFile;
use Attach\Model\FileEntity;

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
            CommandInterface::COMMAND_FIND => ImageFile::class,
            CommandInterface::COMMAND_GET => ImageFile::class,
            CommandInterface::COMMAND_COUNT => ImageFile::class,
            CommandInterface::COMMAND_SAVE => ImageFile::class,
            CommandInterface::COMMAND_DELETE => ImageFile::class,
            CommandInterface::COMMAND_LOAD => ImageFile::class,
            CommandInterface::COMMAND_RESERVE => ImageFile::class,
            CommandInterface::COMMAND_GENERATE_ID => ImageFile::class,
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
];

<?php

use DeltaPhp\Operator\WorkersContainerInterface;
use Articles\Model\Article;
use DeltaPhp\Operator\Command\CommandInterface;
use \DeltaPhp\Operator\Worker\WorkerInterface;
use \DeltaPhp\Operator\Command\RelationLoadCommand;
use \Articles\Model\ArticleImageRelation;
use Attach\Model\ImageFileEntity;
use \DeltaPhp\TagsDictionary\Entity\Tag;
use Articles\Model\ArticleTagRelation;

return [
    "ArticleWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \Articles\Model\ArticleWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 12,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_FIND => Article::class,
            CommandInterface::COMMAND_GET => Article::class,
            CommandInterface::COMMAND_COUNT => Article::class,
            CommandInterface::COMMAND_SAVE => Article::class,
            CommandInterface::COMMAND_DELETE => Article::class,
            CommandInterface::COMMAND_LOAD => Article::class,
            CommandInterface::COMMAND_RESERVE => Article::class,
            CommandInterface::COMMAND_GENERATE_ID => Article::class,
            \DeltaPhp\Operator\Worker\TranslatorObjectToDataWorker::COMMAND_BEFORE_DELETE => [Article::class => -10],
            CommandInterface::COMMAND_WORKER_INFO => Article::class,
        ],
    ],

    "ArticleImagesWorker" => [
        function (WorkersContainerInterface $s) {
            $worker = new \DeltaPhp\Operator\Worker\RelationsWorker(Article::class, ImageFileEntity::class, ArticleImageRelation::class, "article_images_relations");
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $worker->setAdapter($adapter);
            return $worker;
        },
        WorkerInterface::PARAM_TABLEID => 101,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            RelationLoadCommand::COMMAND_RELATION_LOAD => ArticleImageRelation::class,
            CommandInterface::COMMAND_FIND => ArticleImageRelation::class,
            CommandInterface::COMMAND_LOAD => ArticleImageRelation::class,
            CommandInterface::COMMAND_RESERVE => ArticleImageRelation::class,
            CommandInterface::COMMAND_GENERATE_ID => ArticleImageRelation::class,
            CommandInterface::COMMAND_GET => ArticleImageRelation::class,
            CommandInterface::COMMAND_COUNT => ArticleImageRelation::class,
            CommandInterface::COMMAND_SAVE => ArticleImageRelation::class,
            CommandInterface::COMMAND_DELETE => ArticleImageRelation::class,
            \DeltaPhp\Operator\Command\RelationParamsCommand::COMMAND_RELATION_PARAMS => ArticleImageRelation::class,
            CommandInterface::COMMAND_WORKER_INFO => ArticleImageRelation::class,
        ],
    ],

    "ArticleTagWorker" => [
        function (WorkersContainerInterface $s) {
            $worker = new \DeltaPhp\Operator\Worker\RelationsWorker(Article::class, Tag::class, ArticleTagRelation::class, "article_tags_relations");
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $worker->setAdapter($adapter);
            return $worker;
        },
        WorkerInterface::PARAM_TABLEID => 102,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            RelationLoadCommand::COMMAND_RELATION_LOAD => ArticleTagRelation::class,
            CommandInterface::COMMAND_FIND => ArticleTagRelation::class,
            CommandInterface::COMMAND_LOAD => ArticleTagRelation::class,
            CommandInterface::COMMAND_RESERVE => ArticleTagRelation::class,
            CommandInterface::COMMAND_GENERATE_ID => ArticleTagRelation::class,
            CommandInterface::COMMAND_GET => ArticleTagRelation::class,
            CommandInterface::COMMAND_COUNT => ArticleTagRelation::class,
            CommandInterface::COMMAND_SAVE => ArticleTagRelation::class,
            CommandInterface::COMMAND_DELETE => ArticleTagRelation::class,
            \DeltaPhp\Operator\Command\RelationParamsCommand::COMMAND_RELATION_PARAMS => ArticleTagRelation::class,
            CommandInterface::COMMAND_WORKER_INFO => ArticleTagRelation::class,
        ],
    ],
];

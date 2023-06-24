<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Entity\ContentEntity;

class ContentEntitiesWorker extends NamedEntitiesWorker
{
    public const WORKER_ID = 'contentEntitiesWorker';

    public const TABLE_NAME = 'content';
    protected const FIELDS = ['content'];

    public const ENTITY = ContentEntity::class;
}

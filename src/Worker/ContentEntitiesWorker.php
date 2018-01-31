<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Entity\ContentEntity;

class ContentEntitiesWorker extends NamedEntitiesWorker
{
    const WORKER_ID = 'contentEntitiesWorker';
    const TABLE_NAME = 'content';
    const FIELDS = ['content'];

    public static function getEntityClassForMapFilter()
    {
        return ContentEntity::class;
    }
}

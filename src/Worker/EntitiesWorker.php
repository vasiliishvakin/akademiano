<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Entity\Entity;

class EntitiesWorker extends PostgresEntityWorker
{
    const WORKER_ID = 'entitiesWorker';
    const TABLE_ID = 1;
    const TABLE_NAME = "entities";
    const FIELDS = ['id', 'created', 'changed', 'owner',];
    const UNMERGED_FIELDS = [ 'id', 'created', 'owner',];
    const EXT_ENTITY_FIELDS = ['owner'];

    public static function getEntityClassForMapFilter()
    {
        return Entity::class;
    }
}

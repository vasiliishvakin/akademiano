<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Entity\NamedEntity;

class NamedEntitiesWorker extends EntitiesWorker
{
    const WORKER_ID = 'namedEntitiesWorker';
    const TABLE_NAME = 'named';
    const FIELDS = [ 'title', 'description'];

    public static function getEntityClassForMapFilter()
    {
        return NamedEntity::class;
    }
}

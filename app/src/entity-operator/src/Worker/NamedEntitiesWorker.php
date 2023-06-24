<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Entity\NamedEntity;

class NamedEntitiesWorker extends EntitiesWorker
{
    public const WORKER_ID = 'namedEntitiesWorker';
    public const TABLE_NAME = 'named';
    protected const FIELDS = [ 'title', 'description'];

    public const ENTITY = NamedEntity::class;

}

<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Entity\Entity;

class EntitiesWorker extends PostgresEntityWorker
{
    public const WORKER_ID = 'entitiesWorker';

    public const TABLE_NAME = "entities";
    protected const FIELDS = ['id', 'created', 'changed', 'owner',];
    protected const UNMERGED_FIELDS = [ 'id', 'created', 'owner',];
    protected const EXT_ENTITY_FIELDS = ['owner'];

    public const ENTITY = Entity::class;
}

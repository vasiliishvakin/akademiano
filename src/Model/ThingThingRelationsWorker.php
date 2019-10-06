<?php


namespace Akademiano\Content\Knowledgebase\Thing\Model;


use Akademiano\EntityOperator\Worker\RelationsWorker;

class ThingThingRelationsWorker extends RelationsWorker
{
    public const WORKER_ID = 'thinThingRelationsWorker';
    public const TABLE_NAME = 'relations_thing_thing';
    public const ENTITY = ThingRelation::class;
}

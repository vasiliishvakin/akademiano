<?php


namespace Akademiano\Attach\Model;


use Akademiano\EntityOperator\Worker\RelationsWorker;
use Akademiano\Entity\Entity;

class EntityFileRelationWorker extends RelationsWorker
{
    const WORKER_NAME = "entityFileRelationWorker";

    const FIRST_CLASS = EntityFileRelation::FIRST_CLASS;
    const SECOND_CLASS = EntityFileRelation::SECOND_CLASS;

    const TABLE_ID = LinkedFilesWorker::TABLE_ID + 1;
    const TABLE_NAME = "entity_file_relations";
    const FIELDS = ["first", "second"];
    const ENTITY_CLASS = EntityFileRelation::class;
}

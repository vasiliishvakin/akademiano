<?php


namespace Akademiano\Attach\Model;


use Akademiano\EntityOperator\Worker\RelationsWorker;
use Akademiano\Entity\Entity;

class EntityFileRelationWorker extends RelationsWorker
{
    const FIRST_CLASS = EntityFileRelation::FIRST_CLASS;
    const SECOND_CLASS = EntityFileRelation::SECOND_CLASS;

    const TABLE_ID = FilesWorker::TABLE_ID + self::TABLE_ID_INC;
    const TABLE_NAME = "entity_file_relations";
    const EXPAND_FIELDS = ["first", "second"];
    const ENTITY_CLASS = EntityFileRelation::class;
}

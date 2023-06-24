<?php


namespace Akademiano\Content\Tags\Model;


use Akademiano\EntityOperator\Worker\RelationsWorker;

class TagsRelationsWorker extends RelationsWorker
{
    const FIRST_CLASS = TagRelation::FIRST_CLASS;
    const SECOND_CLASS = TagRelation::SECOND_CLASS;

    const WORKER_ID = 'tagRelationsWorker';
    const TABLE_NAME = 'tags_tags_relations';
    const TABLE_ID = DictionariesWorker::TABLE_ID + 1;

    public static function getEntityClassForMapFilter()
    {
        return TagRelation::class;
    }
}

<?php


namespace Akademiano\Content\Tags\Model;


class TagsDictionariesRelationWorker extends TagsRelationsWorker
{
    const FIRST_CLASS = TagDictionaryRelation::FIRST_CLASS;
    const SECOND_CLASS = TagDictionaryRelation::SECOND_CLASS;

    const WORKER_ID = 'tagsDictionariesRelationsWorker';
    const TABLE_ID = TagsRelationsWorker::TABLE_ID + 1;
    const TABLE_NAME = 'tags_tags_dictionaries_relations';

    public static function getEntityClassForMapFilter()
    {
        return TagDictionaryRelation::class;
    }
}

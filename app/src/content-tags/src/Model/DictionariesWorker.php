<?php


namespace Akademiano\Content\Tags\Model;


use Akademiano\EntityOperator\Worker\NamedEntitiesWorker;

class DictionariesWorker extends NamedEntitiesWorker
{
    const WORKER_ID = 'dictionariesWorker';
    const TABLE_ID = TagsWorker::TABLE_ID + 1;
    const TABLE_NAME = "tags_dictionaries";

    public static function getEntityClassForMapFilter()
    {
        return Dictionary::class;
    }

}

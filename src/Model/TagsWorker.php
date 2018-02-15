<?php


namespace Akademiano\Content\Tags\Model;


use Akademiano\EntityOperator\Worker\NamedEntitiesWorker;

class TagsWorker extends NamedEntitiesWorker
{
    const WORKER_ID = 'tagsWorker';
    const TABLE_ID = 130;
    const TABLE_NAME = "tags_tags";
    const FIELDS = ['dictionaries'];
    const UNSAVED_FIELDS = ['dictionaries'];


    public static function getEntityClassForMapFilter()
    {
        return Tag::class;
    }
}

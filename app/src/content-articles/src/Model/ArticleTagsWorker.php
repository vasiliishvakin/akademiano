<?php


namespace Akademiano\Content\Articles\Model;


use Akademiano\Content\Tags\Model\TagsWorker;

class ArticleTagsWorker extends TagsWorker
{
    const WORKER_ID = 'articleTagsWorker';

    public static function getEntityClassForMapFilter()
    {
        return ArticleTag::class;
    }
}

<?php


namespace Akademiano\Content\Articles\Model;


use Akademiano\Content\Tags\Model\TagsRelationsWorker;

class TagsArticlesRelationsWorker extends TagsRelationsWorker
{
    const FIRST_CLASS = TagArticleRelation::FIRST_CLASS;
    const SECOND_CLASS = TagArticleRelation::SECOND_CLASS;

    const WORKER_ID = 'tagsArticlesRelationsWorker';
    const TABLE_NAME = 'article_tags_articles_relations';
    const TABLE_ID = ArticleImagesWorker::TABLE_ID + 1;

    public static function getEntityClassForMapFilter()
    {
        return TagArticleRelation::class;
    }
}

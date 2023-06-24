<?php


namespace Akademiano\Content\Articles\Api\v1;


use Akademiano\Content\Articles\Model\TagArticleRelation;
use Akademiano\Content\Tags\Api\v1\TagsRelationsApi;

class TagsArticlesRelationsApi extends TagsRelationsApi
{
    const ENTITY_CLASS = TagArticleRelation::class;
    const API_ID = "tagsArticlesRelationsApi";
}

<?php


namespace Akademiano\Content\Articles\Api\v1;


use Akademiano\Content\Articles\Model\ArticleTag;
use Akademiano\Content\Tags\Api\v1\TagsApi;

class ArticleTagsApi extends TagsApi
{
    const ENTITY_CLASS = ArticleTag::class;
    const API_ID = "articleTagsApi";
}

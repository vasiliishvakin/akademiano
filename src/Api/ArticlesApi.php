<?php


namespace Akademiano\Content\Articles\Articles\Api;


use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\Content\Articles\Model\Article;

class ArticlesApi extends EntityApi
{
    const ENTITY_CLASS = Article::class;
    const API_ID = "articlesApi";
    const DEFAULT_ORDER = ["id" => "DESC"];

}

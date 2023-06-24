<?php


namespace Akademiano\Content\Knowledgebase\It\Api\v1;


use Akademiano\Api\v1\Entities\CompositeEntityApi;
use Akademiano\Content\Articles\Api\v1\ArticlesApi;
use Akademiano\Content\Articles\Model\Article;
use Akademiano\Content\Knowledgebase\It\Model\Thing;
use Akademiano\Entity\EntityInterface;

class ThingsApi extends ArticlesApi
{
    const ENTITY_CLASS = Thing::class;
    const API_ID = "thingsApi";
    const DEFAULT_ORDER = ["id" => "DESC"];

    const RELATIONS = [
        //'tags' => TagsArticlesRelationsApi::API_ID,
    ];

    /** @var  ThingImagesApi */
    protected $filesApi;

    //TODO Dirty hack
    public function getRelatedAttributes(): array
    {
        return [];
    }
}

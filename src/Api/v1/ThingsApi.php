<?php


namespace Akademiano\Content\Knowledgebase\Thing\Api\v1;


use Akademiano\Api\v1\Entities\CompositeEntityApi;
use Akademiano\Content\Articles\Model\Article;
use Akademiano\Content\Knowledgebase\Thing\Model\Thing;
use Akademiano\Entity\EntityInterface;

class ThingsApi extends CompositeEntityApi
{
    const ENTITY_CLASS = Thing::class;
    const API_ID = "thingsApi";
    const DEFAULT_ORDER = ["id" => "DESC"];

    /** @var  ThingImagesApi */
    protected $filesApi;


    public function getFilesApi(): ThingImagesApi
    {
        return $this->filesApi;
    }

    public function setFilesApi(ThingImagesApi $filesApi)
    {
        $this->filesApi = $filesApi;
    }
}

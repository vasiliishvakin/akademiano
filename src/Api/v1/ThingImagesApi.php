<?php


namespace Akademiano\Content\Knowledgebase\It\Api\v1;


use Akademiano\Content\Articles\Api\v1\ArticleImagesApi;
use Akademiano\Content\Knowledgebase\It\Module;
use Akademiano\Content\Knowledgebase\It\Model\ThingImage;

class ThingImagesApi extends ArticleImagesApi
{
    const API_ID = "thingImagesApi";
    const ENTITY_CLASS = ThingImage::class;
    const MODULE_ID = Module::MODULE_ID;
    const IS_PUBLIC = true;
}

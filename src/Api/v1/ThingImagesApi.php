<?php


namespace Akademiano\Content\Knowledgebase\Thing\Api\v1;


use Akademiano\Content\Articles\Api\v1\ArticleImagesApi;
use Akademiano\Content\Knowledgebase\Thing\Module;
use Akademiano\Content\Knowledgebase\Thing\Model\ThingImage;

class ThingImagesApi extends ArticleImagesApi
{
    const API_ID = "thingImagesApi";
    const ENTITY_CLASS = ThingImage::class;
    const MODULE_ID = Module::MODULE_ID;
    const IS_PUBLIC = true;
}

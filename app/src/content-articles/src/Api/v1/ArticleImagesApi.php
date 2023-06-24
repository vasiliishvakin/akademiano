<?php


namespace Akademiano\Content\Articles\Api\v1;


use Akademiano\Content\Articles\Model\ArticleImage;
use Akademiano\Content\Articles\Module;
use Akademiano\Content\Files\Images\Api\v1\LinkedImagesApi;

class ArticleImagesApi extends LinkedImagesApi
{
    const API_ID = "articleImagesApi";
    const ENTITY_CLASS = ArticleImage::class;
    const MODULE_ID = Module::MODULE_ID;
    const IS_PUBLIC = true;
}

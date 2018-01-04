<?php


namespace Akademiano\Content\Articles\Api\v1;


use Akademiano\Attach\Api\v1\LinkedFilesApi;
use Akademiano\Content\Articles\Model\ArticleFile;
use Akademiano\Content\Articles\Module;

class ArticleFilesApi extends LinkedFilesApi
{
    const API_ID = "articleFilesApi";
    const ENTITY_CLASS = ArticleFile::class;
    const MODULE_ID = Module::MODULE_ID;
    const IS_PUBLIC = true;
}

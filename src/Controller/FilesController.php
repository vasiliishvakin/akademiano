<?php


namespace Akademiano\Content\Articles\Controller;


use Akademiano\Content\Articles\Api\v1\ArticleFilesApi;
use Akademiano\Content\Articles\RoutesStore;

class FilesController extends \Akademiano\Content\Files\Controller\FilesController
{
    const ENTITY_API_ID = ArticleFilesApi::API_ID;
    const INTERNAL_URL_PREFIX = 'files';
    const ROUTE_FILE_BY_NAME = RoutesStore::FILE_VIEW_ROUTE;
}

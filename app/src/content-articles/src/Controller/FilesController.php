<?php


namespace Akademiano\Content\Articles\Controller;


use Akademiano\Content\Articles\Api\v1\ArticleImagesApi;
use Akademiano\Content\Articles\RoutesStore;

class FilesController extends \Akademiano\Content\Files\Controller\FilesController
{
    const ENTITY_API_ID = ArticleImagesApi::API_ID;
    const INTERNAL_URL_PREFIX = 'data/files';
    const ROUTE_FILE_BY_NAME = RoutesStore::FILE_VIEW_ROUTE;
}

<?php


namespace Akademiano\Content\Knowledgebase\It\Controller;


use Akademiano\Content\Knowledgebase\It\RoutesStore;
use Akademiano\Content\Knowledgebase\It\Api\v1\ThingImagesApi;

class FilesController extends \Akademiano\Content\Files\Controller\FilesController
{
    const ENTITY_API_ID = ThingImagesApi::API_ID;
    const INTERNAL_URL_PREFIX = 'data/files';
    const ROUTE_FILE_BY_NAME = RoutesStore::FILE_VIEW_ROUTE;
}

<?php

namespace Akademiano\Content\Knowledgebase\It\Controller;


use Akademiano\Content\Knowledgebase\It\RoutesStore;
use Akademiano\Content\Knowledgebase\It\Api\v1\ThingsApi;

/**
 * @method ThingsApi getEntityApi()
 */
class IndexController extends \Akademiano\Content\Articles\Controller\IndexController
{
    const ENTITY_OPSR_STORE_CLASS = RoutesStore::class;
    const ENTITY_API_ID = ThingsApi::API_ID;
    const FORM_FILES_FIELD = "files";
}

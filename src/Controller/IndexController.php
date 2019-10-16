<?php

namespace Akademiano\Content\Knowledgebase\Thing\Controller;


use Akademiano\Content\Knowledgebase\Thing\RoutesStore;
use Akademiano\Content\Knowledgebase\Thing\Api\v1\ThingsApi;

/**
 * @method ThingsApi getEntityApi()
 */
class IndexController extends \Akademiano\Content\Articles\Controller\IndexController
{
    const ENTITY_OPSR_STORE_CLASS = RoutesStore::class;
    const ENTITY_API_ID = ThingsApi::API_ID;
    const FORM_FILES_FIELD = "files";
}

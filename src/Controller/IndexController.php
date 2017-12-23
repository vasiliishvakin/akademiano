<?php

namespace Akademiano\Content\Articles\Controller;


use Akademiano\Content\Articles\Articles\Api\ArticlesApi;
use Akademiano\Content\Articles\RoutesStore;
use Akademiano\EntityOperator\Ext\Controller\AkademianoEntityController;

class IndexController extends AkademianoEntityController
{
    const ENTITY_OPSR_STORE_CLASS = RoutesStore::class;
    const ENTITY_API_ID = ArticlesApi::API_ID;
}

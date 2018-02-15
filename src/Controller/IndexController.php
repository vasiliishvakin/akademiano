<?php

namespace Akademiano\Content\Articles\Controller;


use Akademiano\Content\Articles\Api\v1\ArticlesApi;
use Akademiano\Content\Articles\RoutesStore;
use Akademiano\EntityOperator\Ext\Controller\AkademianoCompositeEntityController;

/**
 * @method ArticlesApi getEntityApi()
 */
class IndexController extends AkademianoCompositeEntityController
{
    const ENTITY_OPSR_STORE_CLASS = RoutesStore::class;
    const ENTITY_API_ID = ArticlesApi::API_ID;
    const FORM_FILES_FIELD = "files";

    public function getListCriteria()
    {
        return [];
    }
}

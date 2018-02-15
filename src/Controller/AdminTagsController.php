<?php

namespace Akademiano\Content\Tags\Controller;

use Akademiano\Content\Tags\AdminTagsRoutesStore;
use Akademiano\Content\Tags\Api\v1\DictionariesApi;
use Akademiano\Content\Tags\Api\v1\TagsApi;
use Akademiano\EntityOperator\Ext\Controller\AkademianoCompositeEntityController;

class AdminTagsController extends AkademianoCompositeEntityController
{
    const ENTITY_OPSR_STORE_CLASS = AdminTagsRoutesStore::class;
    const ENTITY_API_ID = TagsApi::API_ID;
    const RELATIONS = [
        'dictionaries' => DictionariesApi::API_ID,
    ];
}

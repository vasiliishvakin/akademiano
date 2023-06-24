<?php

namespace Akademiano\Content\Tags\Controller;

use Akademiano\Content\Tags\AdminDictionariesRoutesStore;
use Akademiano\Content\Tags\Api\v1\DictionariesApi;
use Akademiano\EntityOperator\Ext\Controller\AkademianoEntityController;

class AdminDictionariesController extends AkademianoEntityController
{
    const ENTITY_OPSR_STORE_CLASS = AdminDictionariesRoutesStore::class;
    const ENTITY_API_ID = DictionariesApi::API_ID;
}

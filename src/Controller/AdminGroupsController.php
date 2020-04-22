<?php

namespace Akademiano\UserEO\Controller;


use Akademiano\EntityOperator\Ext\Controller\AkademianoEntityCrudlController;
use Akademiano\UserEO\Api\v1\GroupsApi;
use Akademiano\UserEO\GroupsOpsRoutesStore;

class AdminGroupsController extends AkademianoEntityCrudlController
{
    const ENTITY_OPSR_STORE_CLASS = GroupsOpsRoutesStore::class;
    const ENTITY_API_ID = GroupsApi::API_ID;
    const DEFAULT_LIST_CRITERIA = [];
}

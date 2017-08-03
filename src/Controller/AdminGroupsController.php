<?php

namespace Akademiano\UserEO\Controller;


use Akademiano\EntityOperator\Ext\Controller\AkademianoEntityController;
use Akademiano\UserEO\Api\v1\GroupsApi;
use Akademiano\UserEO\GroupsOpsRoutesStore;

class AdminGroupsController extends AkademianoEntityController
{
    const ENTITY_OPSR_STORE_CLASS = GroupsOpsRoutesStore::class;
    const ENTITY_API_ID = GroupsApi::API_ID;
    const DEFAULT_LIST_CRITERIA = [];
}

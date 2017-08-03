<?php

namespace Akademiano\UserEO\Controller;


use Akademiano\EntityOperator\Ext\Controller\AkademianoEntityController;
use Akademiano\UserEO\Api\v1\GroupsApi;
use Akademiano\UserEO\Api\v1\UsersApi;
use Akademiano\UserEO\UsersOpsRoutesStore;

class AdminUsersController extends AkademianoEntityController
{
    const ENTITY_OPSR_STORE_CLASS = UsersOpsRoutesStore::class;
    const ENTITY_API_ID = UsersApi::API_ID;
    const DEFAULT_LIST_CRITERIA = [];

    const GROUPS_API_ID = GroupsApi::API_ID;

    /**
     * @return GroupsApi
     */
    public function getGroupsApi()
    {
        return $this->getDiContainer()[static::GROUPS_API_ID];
    }

    public function formAction(array $params = [])
    {
        $data = parent::formAction($params);
        $groups = $this->getGroupsApi()->find([]);
        $data["groups"] = $groups;

        return $data;
    }
}

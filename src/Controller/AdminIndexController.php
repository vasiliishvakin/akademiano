<?php

namespace Akademiano\HeraldMessages\Controller;


use Akademiano\EntityOperator\Ext\Controller\AkademianoEntityController;
use Akademiano\HeraldMessages\MessagesOpsRoutesStore;
use Akademiano\HeraldMessages\Api\v1\MessagesApi;
use Akademiano\UserEO\Api\v1\UsersApi;

class AdminIndexController extends AkademianoEntityController
{
    const ENTITY_OPSR_STORE_CLASS = MessagesOpsRoutesStore::class;
    const ENTITY_API_ID = MessagesApi::API_ID;
    const DEFAULT_LIST_CRITERIA = [];

    const USERS_API = UsersApi::API_ID;

    public function getUsersApi()
    {
        return $this->getDiContainer()[static::USERS_API];
    }

    public function formAction(array $params = [])
    {
        $data = parent::formAction($params);
        $users = $this->getUsersApi()->find([]);
        $data["users"] = $users;

        return $data;
    }
}

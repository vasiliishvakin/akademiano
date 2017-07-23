<?php

namespace Akademiano\UserEO\Controller;


use Akademiano\EntityOperator\Ext\Controller\AkademianoEntityController;

class AdminUsersController extends AkademianoEntityController
{
    const ADMIN_USERS_LIST_ROUTE = "admin_users_list";
    const ADMIN_USERS_VIEW_ROUTE = "admin_users_view";

    public function getEntityApi()
    {
        return $this->getDIContainer()["usersApi"];
    }

    public function getListRoute()
    {
        return self::ADMIN_USERS_LIST_ROUTE;
    }

    public function getViewRoute()
    {
        return self::ADMIN_USERS_VIEW_ROUTE;
    }

    public function getListCriteria()
    {
        return [];
    }
}

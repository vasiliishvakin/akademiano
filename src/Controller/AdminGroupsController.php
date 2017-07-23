<?php

namespace Akademiano\UserEO\Controller;


use Akademiano\EntityOperator\Ext\Controller\AkademianoEntityController;

class AdminGroupsController extends AkademianoEntityController
{
    const ADMIN_GROUPS_LIST_ROUTE = "admin_groups_list";
    const ADMIN_GROUPS_VIEW_ROUTE = "admin_groups_view";

    public function getEntityApi()
    {
        return $this->getDIContainer()["groupsApi"];
    }

    public function getListRoute()
    {
        return self::ADMIN_GROUPS_LIST_ROUTE;
    }

    public function getViewRoute()
    {
        return self::ADMIN_GROUPS_VIEW_ROUTE;
    }

    public function getListCriteria()
    {
        return [];
    }
}

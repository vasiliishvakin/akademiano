<?php


namespace Akademiano\UserEO;

use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

class UsersOpsRoutesStore extends EntityOpsRoutesStore
{
    const LIST_ROUTE = "admin_users_list";
    const VIEW_ROUTE = "admin_users_view";
    const ADD_ROUTE = "admin_users_add";
    const EDIT_ROUTE = "admin_users_edit";
    const SAVE_ROUTE = "admin_users_save";
    const DELETE_ROUTE = "admin_users_delete";

    const LOGIN_ROUTE = "login";
    const LOGOUT_ROUTE = "logout";

    public function getLoginRoute()
    {
        return static::LOGIN_ROUTE;
    }

    public function getLogoutRoute()
    {
        return static::LOGOUT_ROUTE;
    }

    public function toArray()
    {
        $result = parent::toArray();
        $result["login_route"] = $this->getLoginRoute();
        $result["logout_route"] = $this->getLogoutRoute();
        return $result;
    }
}

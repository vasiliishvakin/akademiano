<?php


namespace Akademiano\UserEO;

use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

class UsersOpsRoutesStore extends EntityOpsRoutesStore
{
    const LOGIN_ROUTE_NAME = "login_route";
    const LOGOUT_ROUTE_NAME = "logout_route";

    const LIST_ROUTE = "admin_users_list";
    const VIEW_ROUTE = "admin_users_view";
    const ADD_ROUTE = "admin_users_add";
    const EDIT_ROUTE = "admin_users_edit";
    const SAVE_ROUTE = "admin_users_save";
    const DELETE_ROUTE = "admin_users_delete";

    const LOGIN_ROUTE = "login";
    const LOGOUT_ROUTE = "logout";
    const PROFILE_ROUTE = "profile";

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
        if (null === $this->array) {
            $array = parent::toArray();
            if (null !== $this->getLoginRoute()) {
                $array[self::LOGIN_ROUTE_NAME] = $this->getLoginRoute();
            }
            if (null !== $this->getLogoutRoute()) {
                $array[self::LOGOUT_ROUTE_NAME] = $this->getLogoutRoute();
            }
            $this->array = $array;
        }
        return $this->array;
    }
}

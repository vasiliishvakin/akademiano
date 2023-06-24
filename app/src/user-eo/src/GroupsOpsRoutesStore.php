<?php


namespace Akademiano\UserEO;

use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

class GroupsOpsRoutesStore extends EntityOpsRoutesStore
{
    const LIST_ROUTE = "admin_groups_list";
    const VIEW_ROUTE = "admin_groups_view";
    const ADD_ROUTE = "admin_groups_add";
    const EDIT_ROUTE = "admin_groups_edit";
    const SAVE_ROUTE = "admin_groups_save";
    const DELETE_ROUTE = "admin_groups_delete";
}

<?php


namespace Akademiano\HeraldMessages;

use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

class AdminRoutes extends EntityOpsRoutesStore
{
    const LIST_ROUTE = "admin_herald_messages_list";
    const VIEW_ROUTE = "admin_herald_messages_view";
    const ADD_ROUTE = "admin_herald_messages_add";
    const EDIT_ROUTE = "admin_herald_messages_edit";
    const SAVE_ROUTE = "admin_herald_messages_save";
    const DELETE_ROUTE = "admin_herald_messages_delete";
}

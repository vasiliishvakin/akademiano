<?php


namespace Akademiano\Mesages;

use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

class MessagesOpsRoutesStore extends EntityOpsRoutesStore
{
    const LIST_ROUTE = "admin_messages_list";
    const VIEW_ROUTE = "admin_messages_view";
    const ADD_ROUTE = "admin_messages_add";
    const EDIT_ROUTE = "admin_messages_edit";
    const SAVE_ROUTE = "admin_messages_save";
    const DELETE_ROUTE = "admin_messages_delete";
}

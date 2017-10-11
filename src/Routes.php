<?php


namespace Akademiano\HeraldMessages;

use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

class Routes extends EntityOpsRoutesStore
{
    const SEND_ROUTE_NAME = 'items_send_route';

    const LIST_ROUTE = "herald_messages_list";
    const VIEW_ROUTE = "herald_messages_view";
    const SAVE_ROUTE = "herald_messages_save";
    const CHANGE_ROUTE = "herald_messages_change";

    const ADD_ROUTE = "herald_messages_add";
    const EDIT_ROUTE = "herald_messages_edit";

    const SEND_ROUTE = "herald_messages_send";


    public function getSendRoute()
    {
        return static::SEND_ROUTE;
    }

    public function toArray()
    {
        $data = parent::toArray();
        $data[static::SEND_ROUTE_NAME] = $this->getSendRoute();
        return $data;
    }
}

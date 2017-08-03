<?php

namespace Akademiano\EntityOperator\Ext;


use Akademiano\Utils\ArrayTools;

class EntityOpsRoutesStore
{
    const LIST_ROUTE = null;
    const VIEW_ROUTE = null;
    const ADD_ROUTE = null;
    const EDIT_ROUTE = null;
    const SAVE_ROUTE = null;
    const DELETE_ROUTE = null;

    /**
     * @return mixed
     */
    public function getListRoute()
    {
        return static::LIST_ROUTE;
    }

    /**
     * @return mixed
     */
    public function getViewRoute()
    {
        return static::VIEW_ROUTE;
    }

    /**
     * @return mixed
     */
    public function getAddRoute()
    {
        return static::ADD_ROUTE;
    }

    /**
     * @return mixed
     */
    public function getEditRoute()
    {
        return static::EDIT_ROUTE;
    }

    /**
     * @return mixed
     */
    public function getSaveRoute()
    {
        return static::SAVE_ROUTE;
    }

    /**
     * @return mixed
     */
    public function getDeleteRoute()
    {
        return static::DELETE_ROUTE;
    }

    public function toArray()
    {
        $result =  [
            "item_list_route" => $this->getListRoute(),
            "item_view_route" => $this->getViewRoute(),
            "item_add_route" => $this->getAddRoute(),
            "item_edit_route" => $this->getEditRoute(),
            "item_save_route" => $this->getSaveRoute(),
            "item_delete_route" => $this->getDeleteRoute(),
        ];
        return ArrayTools::filterNulls($result);
    }
}

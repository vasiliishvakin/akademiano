<?php

namespace Akademiano\EntityOperator\Ext;


use Akademiano\Utils\ArrayTools;

class EntityOpsRoutesStore
{
    const LIST_ROUTE_NAME = "items_list_route";
    const VIEW_ROUTE_NAME = "item_view_route";
    const ADD_ROUTE_NAME = "item_add_route";
    const EDIT_ROUTE_NAME = "item_edit_route";
    const SAVE_ROUTE_NAME = "item_save_route";
    const DELETE_ROUTE_NAME = "item_delete_route";
    const CHANGE_ROUTE_NAME = "item_change_route";

    const LIST_ROUTE = null;
    const VIEW_ROUTE = null;
    const ADD_ROUTE = null;
    const EDIT_ROUTE = null;
    const SAVE_ROUTE = null;
    const DELETE_ROUTE = null;
    const CHANGE_ROUTE = null;

    protected $array;

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

    public function getChangeRoute()
    {
        return static::CHANGE_ROUTE;
    }


    public function toArray()
    {
        if (null === $this->array) {
            $array = [
                self::LIST_ROUTE_NAME => $this->getListRoute(),
                self::VIEW_ROUTE_NAME => $this->getViewRoute(),
                self::ADD_ROUTE_NAME => $this->getAddRoute(),
                self::EDIT_ROUTE_NAME => $this->getEditRoute(),
                self::SAVE_ROUTE_NAME => $this->getSaveRoute(),
                self::DELETE_ROUTE_NAME => $this->getDeleteRoute(),
                self::CHANGE_ROUTE_NAME => $this->getChangeRoute(),
            ];
            $this->array = ArrayTools::filterNulls($array);
        }
        return $this->array;
    }
}

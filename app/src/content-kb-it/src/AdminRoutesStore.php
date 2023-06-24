<?php


namespace Akademiano\Content\Knowledgebase\It;


use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

class AdminRoutesStore extends EntityOpsRoutesStore
{
    const FILE_VIEW_ROUTE_NAME = "item_file_view_route";


    const LIST_ROUTE = "admin_things_list";
    const VIEW_ROUTE = "things_view";
    const ADD_ROUTE = "things_add";
    const EDIT_ROUTE = "things_edit";
    const SAVE_ROUTE = "things_save";
    const DELETE_ROUTE = "things_delete";

    const FILE_VIEW_ROUTE = "things_file";


    public function getFileViewRoute()
    {
        return static::FILE_VIEW_ROUTE;
    }

    public function toArray()
    {
        if (null === $this->array) {
            $array = parent::toArray();
            if (null !== $this->getFileViewRoute()) {
                $array[static::FILE_VIEW_ROUTE_NAME] = $this->getFileViewRoute();
            }
            $this->array = $array;
        }
        return $this->array;
    }
}

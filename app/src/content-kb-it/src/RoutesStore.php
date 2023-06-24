<?php


namespace Akademiano\Content\Knowledgebase\It;


use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

class RoutesStore extends EntityOpsRoutesStore
{
    const FILE_VIEW_ROUTE_NAME = "item_file_view_route";
    const TAG_ROUTE_NAME = "item_tag_route";


    const LIST_ROUTE = "things_list";
    const VIEW_ROUTE = "things_view";
    const ADD_ROUTE = "things_add";
    const EDIT_ROUTE = "things_edit";
    const SAVE_ROUTE = "things_save";
    const DELETE_ROUTE = "things_delete";

    const FILE_VIEW_ROUTE = "things_file";
    const TAG_ROUTE = "things_tag";


    public function getFileViewRoute()
    {
        return static::FILE_VIEW_ROUTE;
    }

    public function getTagRoute()
    {
        return static::TAG_ROUTE;
    }

    public function toArray()
    {
        if (null === $this->array) {
            $array = parent::toArray();
            if (null !== $this->getFileViewRoute()) {
                $array[static::FILE_VIEW_ROUTE_NAME] = $this->getFileViewRoute();
            }
            if (null !== $this->getTagRoute()) {
                $array[static::TAG_ROUTE_NAME] = $this->getTagRoute();
            }
            $this->array = $array;
        }
        return $this->array;
    }
}

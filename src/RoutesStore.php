<?php


namespace Akademiano\Content\Knowledgebase\Thin;


use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

class RoutesStore extends EntityOpsRoutesStore
{
    const FILE_VIEW_ROUTE_NAME = "item_file_view_route";
    const TAG_ROUTE_NAME = "item_tag_route";


    const LIST_ROUTE = "articles_list";
    const VIEW_ROUTE = "articles_view";
    const ADD_ROUTE = "articles_add";
    const EDIT_ROUTE = "articles_edit";
    const SAVE_ROUTE = "articles_save";
    const DELETE_ROUTE = "articles_delete";

    const FILE_VIEW_ROUTE = "articles_file";
    const TAG_ROUTE = "articles_tag";


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

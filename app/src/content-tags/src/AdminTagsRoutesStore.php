<?php


namespace Akademiano\Content\Tags;


use Akademiano\Content\Articles\RoutesStore;

class AdminTagsRoutesStore extends RoutesStore
{
    const LIST_ROUTE = "tags_list";
    const VIEW_ROUTE = "tags_view";
    const ADD_ROUTE = "tags_add";
    const EDIT_ROUTE = "tags_edit";
    const SAVE_ROUTE = "tags_save";
    const DELETE_ROUTE = "tags_delete";
}

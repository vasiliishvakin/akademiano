<?php


namespace Akademiano\Content\Articles;


use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

class RoutesStore extends EntityOpsRoutesStore
{
    const LIST_ROUTE_NAME = "articles_list_route";
    const VIEW_ROUTE_NAME = "article_view_route";
    const ADD_ROUTE_NAME = "article_add_route";
    const EDIT_ROUTE_NAME = "article_edit_route";
    const SAVE_ROUTE_NAME = "article_save_route";
    const DELETE_ROUTE_NAME = "article_delete_route";
    const CHANGE_ROUTE_NAME = "article_change_route";
}

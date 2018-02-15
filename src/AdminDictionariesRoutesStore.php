<?php


namespace Akademiano\Content\Tags;


use Akademiano\Content\Articles\RoutesStore;

class AdminDictionariesRoutesStore extends RoutesStore
{
    const LIST_ROUTE = "dictionaries_list";
    const VIEW_ROUTE = "dictionaries_view";
    const ADD_ROUTE = "dictionaries_add";
    const EDIT_ROUTE = "dictionaries_edit";
    const SAVE_ROUTE = "dictionaries_save";
    const DELETE_ROUTE = "dictionaries_delete";
}

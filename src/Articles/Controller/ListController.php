<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Articles\Controller;


class ListController extends IndexController
{
    public function listAction()
    {
        $manager = $this->getArticlesManager();
        $count = $manager->count();
        $itemsPerPage = $this->getConfig(["Articles", "itemsPerPageInList"], 200);
        $pageInfo = $this->getPageInfo($count, $itemsPerPage);
        $orderBy = "id";
        $items = $manager->find([], null, $pageInfo["perPage"], $pageInfo["offsetForPage"], $orderBy);
        $this->getView()->assign("items", $items);
        $this->getView()->assignArray($pageInfo);
        $this->getView()->assign("countItems", $count);
        $this->getView()->assign("pageTitle", "Полный список статей gisNote");
        $this->getView()->assign("pageDescription", "Список статей gisNote" );
        $changed = $manager->getLastChangedDate();
        $this->getResponse()->setModified($changed);
    }
} 
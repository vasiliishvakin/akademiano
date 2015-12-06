<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Articles\Controller;


use Articles\Model\Parts\GetArticlesManager;
use DeltaCore\AbstractController;
use DeltaDb\Adapter\WhereParams\Between;
use DeltaUtils\ArrayUtils;
use DeltaUtils\Time;

class IndexController extends AbstractController
{
    use GetArticlesManager;

    public function listAction(array $params = [])
    {
        $manager = $this->getArticlesManager();
        $itemsPerPage = $this->getConfig(["Articles", "itemsPerPage"], 10);
        $section = ArrayUtils::get($params, ["section"]);
        $criteria = [];
        $categories = $manager->getCategories();
        $defaultMetaStart = "Статьи";
        switch ($section) {
            case "category" :
                $categoryId = ArrayUtils::get($params, ["id"]);
                if (empty($categoryId)) {
                    $this->getResponse()->redirect("/articles");
                }
                $cm = $manager->getCategoryManager();
                $category = $cm->findById($categoryId);
                $this->getView()->assign("currentCategory", $category);
                $criteria = ["category" => $categoryId];

                $viewCats = [];
                foreach($categories as $categoryItem) {
                    $active = $id = $category->getId();
                    $viewCats[] = ["id" => $categoryItem->getId(), "name" => $categoryItem->getName(), "active" => $active];
                }
                $categories = $viewCats;
                $defaultMetaStart = "Статьи о " .$category->getName();
                break;
            /*case "archive" :
                $dateStr = $this->getRequest()->getUriPartByNum(3);
                $date = new \DateTime($dateStr);
                $this->getView()->assign("currentDate", $date);
                $criteria = [];
                if (strlen($dateStr) > 7) {
                    $criteria["created"] = $date->format("Y-m-d");
                } else {
                    $criteria["created"] = new Between($date->format("Y-m-01"), $date->format("Y-m-t"));
                }
                $defaultMetaStart = "Статьи за " . Time::toStrIntl($date, "%B %Y");
                break;*/
        }
        $orderBy = ["id" => "desc"];
        $countArticles = $manager->count($criteria);
        $pageInfo = $this->getPageInfo($countArticles, $itemsPerPage);
        $items = $manager->find($criteria, null, $pageInfo["perPage"], $pageInfo["offsetForPage"], $orderBy);
        $this->getView()->assign("items", $items);
        $this->getView()->assignArray($pageInfo);
        $this->getView()->assign("countItems", $countArticles);
        $defaultMetaEnd = $pageInfo["page"] == 1 ? "" : " страница " . $pageInfo["page"];
        $titleStart = $this->getConfig(["Articles", "seo", "list", "title", "start"], $defaultMetaStart);
        $titleEnd = $this->getConfig(["Articles", "seo", "list", "title", "end"], $defaultMetaEnd);
        $descriptionStart = $this->getConfig(["Articles", "seo", "list", "description", "start"], $defaultMetaStart);
        $descriptionEnd = $this->getConfig(["Articles", "seo", "list", "description", "end"], $defaultMetaEnd);
        $this->getView()->assign("pageTitle", $titleStart . $titleEnd );
        $this->getView()->assign("pageDescription", $descriptionStart . $descriptionEnd );
        $this->getView()->assign("categories", $categories);

        $changed = $manager->getLastChangedDate($criteria);
        $this->getResponse()->setModified($changed, true);
    }

    public function viewAction(array $params = [])
    {
        if(!isset($params["id"])) {
            $this->getResponse()->redirect("/articles");
        }
        $id = $params["id"];
        $id = substr($id, 2);
        $id = (integer) $id;
        $manager = $this->getArticlesManager();
        $item = $manager->findById($id);
        if(!$id) {
            $this->getResponse()->redirect("/");
        }
        $this->getView()->assign("item", $item);
        $this->getView()->assign("pageTitle", "{$item->getTitle()}" );
        $this->getView()->assign("pageDescription", "{$item->getDescription()}" );
        $this->getView()->assign("pageImage", $item->getTitleImage() ? $item->getTitleImage()->getUri("medium") : false);
        $this->getView()->assign("categories", $categories = $manager->getCategories());
        $this->getResponse()->setModified($item->getChanged(), true);
    }

} 
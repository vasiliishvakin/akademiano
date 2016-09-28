<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Articles\Controller;


use Articles\Model\Article;
use Articles\Model\ArticleTagRelation;
use DeltaCore\AbstractController;
use DeltaDb\D2QL\Criteria;
use DeltaPhp\Operator\EntityOperator;
use DeltaDb\D2QL\Join;
use DeltaPhp\Operator\Command\InfoWorkerCommand;
use DeltaPhp\Operator\OperatorDiTrait;
use DeltaPhp\TagsDictionary\Entity\Dictionary;
use DeltaPhp\TagsDictionary\Entity\DictionaryTagRelation;
use DeltaPhp\TagsDictionary\Entity\Tag;
use DeltaRouter\Exception\NotFoundException;

class IndexController extends AbstractController
{
    use OperatorDiTrait;

    public function listAction(array $params = [])
    {
        $operator = $this->getOperator();

        $itemsPerPage = $this->getConfig(["Articles", "itemsPerPage"], 10);

        $criteria = null;
        if (isset($params["tag"]) && isset($params["tagId"])) {
            $tagId = hexdec($params["tagId"]);
            /** @var Criteria $criteria */
            $criteria = $operator->execute(
                new InfoWorkerCommand("relatedCriteria", ArticleTagRelation::class, ["currentClass" => Article::class, "joinType" => Join::TYPE_INNER])
            );
            $tagTable = $operator->execute(
                new InfoWorkerCommand("table", Tag::class)
            );
            $criteria->createWhere($tagTable, "id", $tagId, "=");
        }
        //get articles
        $count = $operator->count(Article::class, $criteria);
        $pageInfo = $this->getPageInfo($count, $itemsPerPage);
        $items = $operator->find(Article::class, [], $pageInfo["perPage"], $pageInfo["offsetForPage"], "id");

        $defaultMetaEnd = $pageInfo["page"] == 1 ? "" : " страница " . $pageInfo["page"];
        $defaultMetaStart = "Статьи";
        $titleStart = $this->getConfig(["Articles", "seo", "list", "title", "start"], $defaultMetaStart);
        $titleEnd = $this->getConfig(["Articles", "seo", "list", "title", "end"], $defaultMetaEnd);
        $descriptionStart = $this->getConfig(["Articles", "seo", "list", "description", "start"], $defaultMetaStart);
        $descriptionEnd = $this->getConfig(["Articles", "seo", "list", "description", "end"], $defaultMetaEnd);

        $this->getView()->assignArray($pageInfo);

        //$changed = $manager->getLastChangedDate($criteria);
        //$this->getResponse()->setModified($changed, true);

        return [
            "items" => $items,
            "countItems" => $count,
            "pageTitle" => $titleStart . $titleEnd,
            "pageDescription" => $descriptionStart . $descriptionEnd,
            "categories" => (new ArticleTags($this->getOperator(), $this->getConfig("Articles")))->getTags(),
        ];
    }

    public function viewAction(array $params = [])
    {
        if (!isset($params["id"])) {
            throw new NotFoundException();
        }
        $id = hexdec($params["id"]);
        /** @var EntityOperator $operator */
        $operator = $this->getOperator();
        $item = $operator->get(Article::class, $id);
        if (!$item) {
            throw new NotFoundException();
        }
        $this->getResponse()->setModified($item->getChanged(), true);

        return [
            "item" => $item,
            "pageTitle" => "{$item->getTitle()}",
            "pageDescription" => "{$item->getDescription()}",
            "pageImage" => $item->getTitleImage() ? $item->getTitleImage()->getUri("medium") : false,
            "categories" => $tags = (new ArticleTags($this->getOperator(), $this->getConfig("Articles")))->getTags(),
        ];
    }
}

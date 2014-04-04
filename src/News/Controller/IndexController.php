<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace News\Controller;

use Acl\Model\Parts\AclController;
use DeltaCore\AbstractController;
use DeltaUtils\FileSystem;
use News\Model\Article;
use News\Model\NewsManager;

class IndexController extends AbstractController
{
    use AclController;

    const ITEMS_PER_PAGE = 2;

    public function checkAccess()
    {
        return $this->isAllow();
    }

    /**
     * @return NewsManager
     */
    public function getNewsManager()
    {
        $app = $this->getApplication();
        return $app["NewsManager"];
    }

    public function listAction()
    {
        $nm = $this->getNewsManager();
        $countNews = $nm->count();
        $pageInfo = $this->getPageInfo($countNews, self::ITEMS_PER_PAGE);
        $criteria = [];
        $orderBy = "id";
        $news = $nm->find($criteria, null, $pageInfo["perPage"], $pageInfo["offsetForPage"], $orderBy);
        $this->getView()->assign("items", $news);
        $this->getView()->assignArray($pageInfo);
        $this->getView()->assign("countItems", $countNews);
    }

    public function categoryListAction()
    {
        $this->setViewTemplate("list");
        $categoryId = $this->getRequest()->getUriPartByNum(4);
        if (empty($categoryId)) {
            $this->getResponse()->redirect("/admin/news");
        }
        $nm = $this->getNewsManager();
        $cm = $nm->getCategoryManager();
        $category = $cm->findById($categoryId);
        $this->getView()->assign("currentCategory", $category);


        $criteria = ["category" => $categoryId];
        $countNews = $nm->count($criteria);
        $pageInfo = $this->getPageInfo($countNews, self::ITEMS_PER_PAGE);
        $orderBy = "id";
        $news = $nm->find($criteria, null, $pageInfo["perPage"], $pageInfo["offsetForPage"], $orderBy);
        $this->getView()->assign("items", $news);
        $this->getView()->assignArray($pageInfo);
        $this->getView()->assign("countItems", $countNews);
    }

    public function addAction()
    {
        $this->getView()->assign("action", "Add");
        $categories = $this->getNewsManager()->getCategories();
        $this->getView()->assign("categories", $categories);
    }

    public function getId()
    {
        return $this->getRequest()->getUriPartByNum(4);
    }

    public function editAction()
    {
        $id = $this->getId();
        $nm = $this->getNewsManager();
        $item = $nm->findById($id);
        $this->getView()->assign("action", "Edit");
        $this->getView()->assign("item", $item);
        $categories = $this->getNewsManager()->getCategories();
        $itemCategories = array_flip($item->getCategoriesIds());
        $viewCats = [];
        foreach($categories as $category) {
            $id = $category->getId();
            $active = isset($itemCategories[$id]);
            $viewCats[] = ["id" => $id, "name" => $category->getName(), "active" => $active];
        }
        $this->getView()->assign("categories", $viewCats);
    }

    public function rmAction()
    {
        $this->autoRenderOff();
        $id = $this->getId();
        $this->getNewsManager()->deleteById($id);
        $this->getResponse()->redirect("/admin/news");
    }

    public function saveAction()
    {
        $this->autoRenderOff();
        //save item
        $request = $this->getRequest();
        $requestParams = $request->getParams();
        $nm = $this->getNewsManager();
        /** @var Article $item */
        $item = isset($requestParams["id"]) ? $nm->findById($requestParams["id"]) : $nm->create();
        if (empty($item)) {
            throw new \LogicException("item not found");
        }
        $nm->load($item, $requestParams);
        $nm->save($item);


        $fm = $nm->getFileManager();
        //rm files
        $filesRm = $request->getParam("filesRm", []);
        foreach ($filesRm as $fileId) {
            $fm->deleteById($fileId);
        }

        //save files
        $maxFileSize = $this->getConfig(["News", "Attach", "Size"], 400*1024);
        $files = $request->getFiles("files", FileSystem::FST_IMAGE, $maxFileSize);
        $filesTitle = $request->getParam("filesTitle", []);
        $filesDescription = $request->getParam("filesDescription", []);
        foreach ($files as $file) {
            $name = $file->getName();
            $fileFieldName = str_replace(".", "_", $name);
            $title = isset($filesTitle[$fileFieldName]) ? $filesTitle[$fileFieldName] : null;
            $description = isset($filesDescription[$fileFieldName]) ? $filesDescription[$fileFieldName] : null;
            $fm->saveFileForObject($item, $file, $title, $description);
        }


        $this->getResponse()->redirect("/admin/news");
    }

} 
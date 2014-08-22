<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Articles\Controller;

use Acl\Model\Parts\AclController;
use Attach\Model\FileManager;
use Attach\Model\Parts\AttachSave;
use DeltaCore\AdminControllerInterface;
use DeltaUtils\FileSystem;
use Articles\Model\Article;
use Articles\Model\ArticlesManager;

class AdminController extends IndexController implements  AdminControllerInterface
{
    use AclController;
    use AttachSave;

    const ITEMS_PER_PAGE = 2;

    public function checkAccess()
    {
        return $this->isAllow();
    }

    /**
     * @return FileManager
     */
    public function getFileManager()
    {
        return $this->getArticlesManager()->getFileManager();
    }


    public function categoryListAction()
    {
        $this->setViewTemplate("list");
        $categoryId = $this->getRequest()->getUriPartByNum(4);
        if (empty($categoryId)) {
            $this->getResponse()->redirect("/admin/articles");
        }
        $nm = $this->getArticlesManager();
        $cm = $nm->getCategoryManager();
        $category = $cm->findById($categoryId);
        $this->getView()->assign("currentCategory", $category);

        $criteria = ["category" => $categoryId];
        $countArticles = $nm->count($criteria);
        $pageInfo = $this->getPageInfo($countArticles, self::ITEMS_PER_PAGE);
        $orderBy = "id";
        $articles = $nm->find($criteria, null, $pageInfo["perPage"], $pageInfo["offsetForPage"], $orderBy);
        $this->getView()->assign("items", $articles);
        $this->getView()->assignArray($pageInfo);
        $this->getView()->assign("countItems", $countArticles);
    }

    public function getId()
    {
        return $this->getRequest()->getUriPartByNum(4);
    }

    public function formAction()
    {
        $id = $this->getId();
        $categories = $this->getArticlesManager()->getCategories();
        if (!empty($id)) {
            $nm = $this->getArticlesManager();
            $item = $nm->findById($id);
            if (!$item) {
                throw new \RuntimeException("Bad item id $id");
            }
            $this->getView()->assign("item", $item);
            $categories = $this->getArticlesManager()->getCategories();
            $itemCategories = array_flip($item->getCategoriesIds());
            $viewCats = [];
            foreach($categories as $category) {
                $id = $category->getId();
                $active = isset($itemCategories[$id]);
                $viewCats[] = ["id" => $id, "name" => $category->getName(), "active" => $active];
            }
            $categories = $viewCats;
        }
        $this->getView()->assign("categories", $categories);
    }

    public function rmAction()
    {
        $this->autoRenderOff();
        $id = $this->getId();
        $this->getArticlesManager()->deleteById($id);
        $this->getResponse()->redirect("/admin/articles");
    }

    public function saveAction()
    {
        $this->autoRenderOff();
        //save item
        $request = $this->getRequest();
        $requestParams = $request->getParams();
        $nm = $this->getArticlesManager();
        /** @var Article $item */
        $item = isset($requestParams["id"]) ? $nm->findById($requestParams["id"]) : $nm->create();
        if (empty($item)) {
            throw new \LogicException("item not found");
        }
        $nm->load($item, $requestParams);
        $nm->save($item);
        $maxFileSize = $this->getConfig(["Articles", "Attach", "Size"], 500*1024);
        $this->processFilesRequest($item, $maxFileSize);
        $this->getResponse()->redirect("/admin/articles");
    }

} 
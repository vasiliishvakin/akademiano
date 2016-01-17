<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Articles\Controller;

use Acl\Model\Parts\AclController;
use Attach\Model\FileManager;
use Attach\Model\Parts\AttachSave;
use DeltaCore\AdminControllerInterface;
use DeltaUtils\ArrayUtils;
use Articles\Model\Article;

class AdminController extends IndexController implements AdminControllerInterface
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


    public function formAction(array $params = [])
    {
        $id = ArrayUtils::get($params, "id");
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
            foreach ($categories as $category) {
                $id = $category->getId();
                $active = isset($itemCategories[$id]);
                $viewCats[] = ["id" => $id, "name" => $category->getName(), "active" => $active];
            }
            $categories = $viewCats;
        }
        $this->getView()->assign("categories", $categories);
    }

    public function rmAction(array $params = [])
    {
        $this->autoRenderOff();
        $id = ArrayUtils::get($params, "id");
        if (empty($id)) {
            throw new \RuntimeException("Bad item id $id");
        }
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
        $maxFileSize = $this->getConfig(["Articles", "Attach", "Size"], 500 * 1024);
        $this->processFilesRequest($item, $maxFileSize);
        $this->getResponse()->redirect("/admin/articles");
    }
}

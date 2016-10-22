<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Articles\Controller;

use Acl\Model\Parts\AclController;
use Articles\Model\ArticleImageRelation;
use Attach\Model\Command\EntityAttachSaveCommand;
use Attach\Model\FileManager;
use Attach\Model\Parts\AttachSave;
use DeltaCore\AdminControllerInterface;
use DeltaPhp\TagsDictionary\Entity\Tag;
use DeltaUtils\ArrayUtils;
use Articles\Model\Article;
use Articles\Model\ArticleTags;
use UUID\Model\UuidComplexShortTables;

class AdminController extends IndexController implements AdminControllerInterface
{
    use AclController;

    const ITEMS_PER_PAGE = 2;

    public function checkAccess()
    {
        return $this->isAllow();
    }

    public function formAction(array $params = [])
    {
        $id = ArrayUtils::get($params, "id");
        $operator = $this->getOperator();
        /** @var Tag[] $categories */
        $tags = (new ArticleTags($operator, $this->getConfig("Articles")))->getTags();
        if (!empty($id)) {
            $id = hexdec($id);
            /** @var Article $item */
            $item = $operator->get(Article::class, $id);
            if (!$item) {
                throw new \RuntimeException("Bad item id $id");
            }
            $this->getView()->assign("item", $item);

            $itemTags = $item->getTags();
            if (!$itemTags->isEmpty()) {
                $itemTags = $itemTags->lists("id", "id");
                $viewTags = [];
                foreach ($tags as $tag) {
                    $id = $tag->getId();
                    $active = isset($itemTags[$id]);
                    $viewTags[] = ["id" => $id, "title" => $tag->getTitle(), "select" => $active];
                }
                $tags = $viewTags;
            }
        }
        $this->getView()->assign("tags", $tags);
    }

    public function rmAction(array $params = [])
    {
        $this->autoRenderOff();
        if (isset($params["id"])) {
            $id = hexdec($params["id"]);
            $operator = $this->getOperator();
            $item = $operator->get(Article::class, $id);
            if (!$item) {
                throw new \RuntimeException("Bad item id {$params["id"]}");
            }
            $this->getOperator()->delete($item);
        }
        $this->getResponse()->redirect("/admin/articles");
    }

    public function saveAction()
    {
        $this->autoRenderOff();
        //save item
        $request = $this->getRequest();
        $operator = $this->getOperator();
        $requestParams = $request->getParams();
        if (isset($requestParams["id"])) {
            $id = $operator->create(UuidComplexShortTables::class, ["value" => $requestParams["id"]]);
            unset($requestParams["id"]);
        }

        if (isset($id)) {
            /** @var Article $item */
            $item = $operator->get(Article::class, $id) ?: $operator->create(Article::class);
        } else {
            $item = $operator->create(Article::class);
        }

        if (empty($item)) {
            throw new \LogicException("item not found");
        }
        $operator->load($item, $requestParams);
        if (isset($requestParams["tags"])) {
            $item->setTags($requestParams["tags"]);
        }
        $item->setId($id);
        if (empty($requestParams["changed"])) {
            $item->setChanged(new \DateTime());
        }
        $operator->save($item);

        $maxFileSize = $this->getConfig(["Articles", "Attach", "Size"], 500 * 1024);

        $this->getOperator();

        $fileCommand = new EntityAttachSaveCommand($item, $request, ArticleImageRelation::class, ["maxFileSize" => $maxFileSize]);
        $this->getOperator()->execute($fileCommand);

        $this->getResponse()->redirect("/admin/articles");
    }
}

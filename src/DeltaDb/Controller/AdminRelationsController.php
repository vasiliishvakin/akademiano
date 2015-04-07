<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Controller;


use DeltaCore\AdminControllerInterface;
use DeltaUtils\StringUtils;

class AdminRelationsController extends IndexRelationsController implements AdminControllerInterface
{

    public function getRelationName()
    {
        return $this->getRequest()->getUriPartByNum(5);
    }

    public function formAction()
    {
        $id = StringUtils::idFromStr($this->getRequest()->getUriPartByNum(6));
        if ($id) {
            $item = $this->getRelationsManager()->findById($id);
            $this->getView()->assign("item", $item);
        }

        $firstItems = $this->getRelationsManager()->getFirstManager()->find();
        $this->getView()->assign("firstItems", $firstItems);

        $secondItems = $this->getRelationsManager()->getSecondManager()->find();
        $this->getView()->assign("secondItems", $secondItems);
    }

    public function saveAction()
    {
        $this->autoRenderOff();
        $manager = $this->getRelationsManager();
        $request = $this->getRequest();
        $requestParams = $request->getParams();
        if (isset($requestParams["id"]) && $requestParams["id"]) {
            unset($requestParams["id"]);
        }
        $itemId = $request->getParam("id");
        $item = $itemId ? $manager->findById($itemId) : $manager->create();
        if (empty($item)) {
            throw new \LogicException("item not found");
        }
        $manager->load($item, $requestParams);
        $manager->save($item);
        $this->getResponse()->redirect("/admin/deltadb/relations/list/" . $this->getRelationName());
    }

    public function rmAction()
    {
        $this->autoRenderOff();
        $manager = $this->getRelationsManager();
        $id = StringUtils::idFromStr($this->getRequest()->getUriPartByNum(6));
        if ($id) {
            $manager->deleteById($id);
        }
        $this->getResponse()->redirect("/admin/deltadb/relations/list/" . $this->getRelationName());
    }
} 
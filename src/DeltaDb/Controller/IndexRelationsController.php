<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Controller;


use DeltaCore\AbstractController;
use DeltaDb\Model\Relations\mnRelationsManager;

class IndexRelationsController extends AbstractController
{
    public function init()
    {
        $this->getView()->assign("relationName", $this->getRelationName());
    }

    public function getRelationName()
    {
        return $this->getRequest()->getUriPartByNum(3);
    }

    /**
     * @return mnRelationsManager
     */
    public function getRelationsManager()
    {
        $rf = $this->getApplication()["relationsFactory"];
        return $rf->getManager($this->getRelationName());
    }

    public function listAction()
    {
        $manager = $this->getRelationsManager();
        $items = $manager->find();

        $this->getView()->assign("items", $items);
    }
} 
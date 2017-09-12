<?php

namespace Akademiano\EntityOperator\Ext\Controller;

use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\Api\v1\Entities\EntityApiInterface;
use Akademiano\Core\Controller\AkademianoController;
use Akademiano\Utils\ArrayTools;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

/**
 * Class AkademianoEntityController
 * @package Akademiano\EntityOperator\Ext\Controller
 * CRUDL functions for entities
 */
abstract class AkademianoEntityController extends AkademianoController
{
    const ENTITY_OPSR_STORE_CLASS = EntityOpsRoutesStore::class;
    const ENTITY_API_ID = "entityApi";
    const DEFAULT_ITEMS_PER_PAGE = 20;
    const DEFAULT_LIST_CRITERIA = null;
    const DEFAULT_ORDER = EntityApi::DEFAULT_ORDER;

    /** @var  EntityOpsRoutesStore */
    protected $entityOpsRoutesStore;
    /**
     * @return EntityOpsRoutesStore
     */
    public function getEntityOpsRoutesStore(){
        if (null === $this->entityOpsRoutesStore) {
            $class = static::ENTITY_OPSR_STORE_CLASS;
            $this->entityOpsRoutesStore = new $class();
        }
        return $this->entityOpsRoutesStore;
    }

    public function init()
    {
        $routes = $this->getEntityOpsRoutesStore()->toArray();
        if (!empty($routes)) {
            $this->getView()->assignArray($this->getEntityOpsRoutesStore()->toArray());
        }
    }

    /**
     * @return EntityApiInterface
     */
    public function getEntityApi()
    {
        return $this->getDiContainer()[static::ENTITY_API_ID];
    }

    public function getItemsPerPage()
    {
        return static::DEFAULT_ITEMS_PER_PAGE;
    }

    public function getListCriteria()
    {
        return static::DEFAULT_LIST_CRITERIA;
    }

    public function getListOrder()
    {
        return static::DEFAULT_ORDER;
    }

    public function listAction()
    {
        $items = $this->getEntityApi()->find($this->getListCriteria(),
            $this->getPage(),
            $this->getListOrder(),
            $this->getItemsPerPage()
        );

        return [
            "items" => $items,
        ];
    }

    public function viewAction(array $params = [])
    {
        $id = ArrayTools::get($params, "id");
        if ($id) {
            $id = hexdec($id);
            $item = $this->getEntityApi()->get($id)->getOrThrow(
                new NotFoundException(sprintf('Not found entity with id "%s"', dechex($id)))
            );
            return ["item" => $item];
        }
    }

    public function formAction(array $params = [])
    {
        return $this->viewAction($params);
    }

    public function saveAction()
    {
        $this->autoRenderOff();
        //save item
        $data = $this->getRequest()->getParams();
        $this->getEntityApi()->save($data);

        $this->redirect($this->getEntityOpsRoutesStore()->getListRoute());
    }

    public function deleteAction(array $params = [])
    {
        $this->autoRenderOff();
        if (!isset($params["id"])) {
            throw new \InvalidArgumentException("Could not remove empty id");
        }
        $id = hexdec($params["id"]);
        $this->getEntityApi()->delete($id);
        $this->redirect($this->getEntityOpsRoutesStore()->getListRoute());
    }
}

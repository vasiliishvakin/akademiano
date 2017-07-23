<?php

namespace Akademiano\EntityOperator\Ext\Controller;

use Akademiano\Api\v1\Entities\EntityApiInterface;
use Akademiano\Core\Controller\AkademianoController;
use Akademiano\Utils\ArrayTools;
use Akademiano\HttpWarp\Exception\NotFoundException;

abstract class AkademianoEntityController extends AkademianoController
{
    /**
     * @return EntityApiInterface
     */
    abstract public function getEntityApi();

    abstract public function getListRoute();

    abstract public function getViewRoute();

    public function getItemsPerPage()
    {
        return 20;
    }

    public function getListCriteria()
    {
        return null;
    }

    public function getListOrder()
    {
        return null;
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

        $this->redirect($this->getListRoute());
    }

    public function deleteAction(array $params = [])
    {
        $this->autoRenderOff();
        if (!isset($params["id"])) {
            throw new \InvalidArgumentException("Could not remove empty id");
        }
        $id = hexdec($params["id"]);
        $this->getEntityApi()->delete($id);
        $this->redirect($this->getListRoute());
    }
}

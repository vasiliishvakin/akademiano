<?php


namespace Akademiano\EntityOperator\Ext\Controller;


use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\Utils\ArrayTools;
use Akademiano\Api\v1\Entities\EntityApiInterface;

trait EntityCrudlTrait
{
//    const DEFAULT_ITEMS_PER_PAGE = 20;
//    const DEFAULT_LIST_CRITERIA = null;

    use EntityOpsRoutesTrait;

    abstract public function getEntityApi(): EntityApiInterface;

    abstract public function getPage();

    abstract public function getRoute();

    abstract public function getRequest();

    abstract public function autoRenderOff();

    abstract public function redirect($routeId, array $params = []);

    abstract public function getUrlParams();



    public function getItemsPerPage(): int
    {
        return static::DEFAULT_ITEMS_PER_PAGE;
    }

    public function getListCriteria(): ?array
    {
        return static::DEFAULT_LIST_CRITERIA;
    }

    public function getListOrder()
    {
        return $this->getEntityApi()->getDefaultOrder();
    }

    public function listAction()
    {
        $page = $this->getPage();
        $items = $this->getEntityApi()->find(
            $this->getListCriteria(),
            $page,
            $this->getListOrder(),
            $this->getItemsPerPage()
        );
        if ($items->count() <= 0 && $page !== 1) {
            throw new NotFoundException(sprintf('Not found items in page %d', $page));
        }

        $result = [
            "items" => $items,
        ];

        if ($page > 1) {
            $url = $this->getRoute()->getUrl($this->getUrlParams());
            $url->getQuery()->setItems([self::PAGE_PARAM_NAME => $page]);
            $result['_url'] = $url;
        }
        return $result;
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
        $id = ArrayTools::get($params, "id");
        if ($id) {
            return $this->viewAction($params);
        } else {
            $fields = $this->getEntityApi()->getFormFields();
            $fieldsJson = array_fill_keys($fields, "");
            $data["fields"] = $fieldsJson;
            return $data;
        }
    }

    public function saveAction()
    {
        $this->autoRenderOff();
        //save item
        $data = $this->getRequest()->getParams();
        if (isset($data["json_data_raw"])) {
            $rawData = json_decode($data["json_data_raw"], true);
            unset($data["json_data_raw"]);
            $data = ArrayTools::mergeRecursive($rawData, $data);
        }
        $result = $this->getEntityApi()->save($data);

        $info = [
            'id' => $result->getId()->getInt(),
            'status' => $result ? 'OK' : 'ERROR',
        ];

        $this->redirect($this->getEntityOpsRoutesStore()->getListRoute());

        return $info;
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

    public function changeAction(array $params = [])
    {
        if ($this->getRequest()->getMethod() === 'POST') {
            $method = strtolower($this->getRequest()->getParam('X-HTTP_REAL_METHOD'));
        } else {
            $method = strtolower($this->getRequest()->getMethod());
        }
        switch ($method) {
            case "delete":
                $this->deleteAction($params);
                break;
            default:
                $this->saveAction();
                break;
        }
    }
}

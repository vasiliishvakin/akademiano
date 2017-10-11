<?php

namespace Akademiano\HeraldMessages\Controller;


use Akademiano\Acl\AclManager;
use Akademiano\Acl\RestrictedAccessInterface;
use Akademiano\EntityOperator\Ext\Controller\AkademianoEntityController;
use Akademiano\HeraldMessages\Api\v1\MessagesApi;
use Akademiano\HeraldMessages\Model\TransportType;
use Akademiano\HeraldMessages\Routes;
use Akademiano\Router\Route;

class MessagesController extends AkademianoEntityController implements RestrictedAccessInterface
{
    const ENTITY_OPSR_STORE_CLASS = Routes::class;
    const ENTITY_API_ID = MessagesApi::API_ID;
    const DEFAULT_LIST_CRITERIA = [];

    public function accessCheck()
    {

        $di = $this->getDiContainer();
        /** @var AclManager $aclManager */
        $aclManager = $di['aclManager'];

        $accept = $this->getRequest()->getEnvironment()->getAccept();
        $accept = reset($accept);
        if ($accept === 'application/json') {
            $key = $this->getRequest()->getParam('apikey');
            if ($key === "2183") {
                $aclManager->disableAccessCheck();
                return true;
            } else {
                return false;
            }
        } else {
            return $aclManager->accessCheck();
        }
    }

    public function getListCriteria()
    {
        $params = $this->getRequest()->getParams();
        $fields = $this->getEntityApi();

        return static::DEFAULT_LIST_CRITERIA;
    }

    public function formAction(array $params = [])
    {
        $data = parent::formAction($params);
        $transports = TransportType::toArray();
        unset($transports['__default']);
        $data['transports'] = $transports;
        return $data;
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
                return $this->deleteAction($params);
                break;
            default:
                $this->saveAction();
                break;
        }
    }
}

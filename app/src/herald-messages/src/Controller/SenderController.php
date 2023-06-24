<?php


namespace Akademiano\HeraldMessages\Controller;


use Akademiano\Acl\AclManager;
use Akademiano\Acl\RestrictedAccessInterface;
use Akademiano\Core\ApplicationController;
use Akademiano\HeraldMessages\Api\v1\SendApi;
use Akademiano\HeraldMessages\Model\Message;
use Akademiano\HttpWarp\Exception\AccessDeniedException;
use Akademiano\HttpWarp\Exception\NotFoundException;

class SenderController extends ApplicationController implements RestrictedAccessInterface
{
    /** @var  SendApi */
    protected $sendApi;

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

    /**
     * @return SendApi
     */
    public function getSendApi(): SendApi
    {
        return $this->getDiContainer()[SendApi::API_ID];
    }

    public function sendAction(array $params = []): array
    {
        $this->getRequest()->getEnvironment()->setAccept('application/json');

        if (!isset($params['id'])) {
            throw new \RuntimeException('Not define id message to send');
        }
        $id = hexdec($params['id']);
        try {
            $result = $this->getSendApi()->send($id);

            $info = [
                'message' => $id,
                'status' => $result > 0 ? 'OK' : 'ERROR',
            ];
        } catch (\Throwable $e) {
            if ($e instanceof NotFoundException) {
                $httpCode = 404;
            } elseif ($e instanceof AccessDeniedException) {
                $httpCode = 403;
            } else {
                $httpCode = 500;
            }

            $this->getResponse()->setCode($httpCode);
            $info = [
                'message' => $id,
                'status' => 'ERROR',
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
            ];
        }
        return $info;
    }
}

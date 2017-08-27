<?php

namespace Akademiano\UserEO\Controller;


use Akademiano\Acl\Adapter\XAclAdapter;
use Akademiano\Acl\RestrictedAccessInterface;
use Akademiano\Core\Controller\AkademianoController;
use Akademiano\Core\Exception\AccessDeniedException;
use Akademiano\User\GuestUser;
use Akademiano\UserEO\Custodian;
use Akademiano\UserEO\UsersOpsRoutesStore;

class ForbiddenController extends AkademianoController implements RestrictedAccessInterface
{
    public function accessCheck()
    {
        return true;
    }


    public function forbiddenAction(array $params = null)
    {
        $this->getResponse()->setCode(403);
        /** @var Custodian $custodian */
        $custodian = $this->getDiContainer()["custodian"];
        if ($custodian->getCurrentUser() instanceof GuestUser) {
            $this->getView()->setTemplate("Akademiano/UserEO/user/login");
            $this->getView()->assign("redirectUrl", (string)$this->getRequest()->getUrl());
        } else {
            if (isset($params["exception"])) {
                /** @var \Exception $exception */
                $exception = $params["exception"];
                $exceptionInfo = [];
                if ($exception instanceof AccessDeniedException) {
                    $resource = $exception->getResource();
                    $resource = XAclAdapter::prepareResource($resource);
                    if (!empty($resource)) {
                        $exceptionInfo["resource"] = $resource;
                    }
                    $url = $exception->getUrl();
                    if (!empty($url)) {
                        $exceptionInfo["url"] = $url;
                    }
                }
                $exceptionInfo["message"] = $exception->getMessage();
                $exceptionInfo["code"] = $exception->getCode();

                $this->getView()->assign("exception", $exceptionInfo);
            }
            $this->getView()->assign("user", $custodian->getCurrentUser());
            $this->getView()->assignArray((new UsersOpsRoutesStore())->toArray());
        }

    }

}

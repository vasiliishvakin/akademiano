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
            if (isset($params["exception"]) && $params["exception"] instanceof AccessDeniedException) {
                /** @var AccessDeniedException $exception */
                $exception = $params["exception"];
                $resource = $exception->getResource();
                $resource = XAclAdapter::prepareResource($resource);
                if (empty($resource)) {
                    $resource = null;
                }
                $url = $exception->getUrl();
                if (empty($url)) {
                    $url = null;
                }
                $this->getView()->assign("exception", [
                    "resource" => $resource,
                    "url" => $url,
                ]);
            }
            $this->getView()->assign("user", $custodian->getCurrentUser());
            $this->getView()->assignArray((new UsersOpsRoutesStore())->toArray());
        }

    }

}

<?php

namespace Akademiano\UserEO\Controller;


use Akademiano\Acl\RestrictedAccessInterface;
use Akademiano\Core\Controller\AkademianoController;
use Akademiano\User\GuestUser;
use Akademiano\UserEO\Custodian;

class ForbiddenController extends AkademianoController implements RestrictedAccessInterface
{
    public function accessCheck()
    {
       return true;
    }


    public function forbiddenAction()
    {
        $this->getResponse()->setCode(403);
        /** @var Custodian $custodian */
        $custodian = $this->getDiContainer()["custodian"];
        if ($custodian->getCurrentUser() instanceof GuestUser) {
            $this->getView()->setTemplate("Akademiano/UserEO/user/login");
            $this->getView()->assign("redirectUrl", (string)$this->getRequest()->getUrl());
        }

    }

}

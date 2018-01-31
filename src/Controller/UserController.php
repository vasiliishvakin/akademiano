<?php

namespace Akademiano\UserEO\Controller;

use Akademiano\Core\Controller\AkademianoController;
use Akademiano\UserEO\Api\v1\UsersApi;
use Akademiano\UserEO\Custodian;
use Akademiano\Utils\Exception\EmptyException;

class UserController extends AkademianoController
{
    public function loginAction()
    {
        $email = $this->getRequest()->getParam("email");
        $password = $this->getRequest()->getParam("password");
        if (empty($email) || empty($password)) {
            return null;
        }
        /** @var Custodian $custodian */
        $custodian = $this->getDiContainer()["custodian"];
        try {
            $auth = $custodian->authenticate($email, $password);
        } catch (EmptyException $e) {
            return null;
        }
        if (!$auth) {
            return null;
        }
        /** @var UsersApi $usersApi */
        $usersApi = $this->getDiContainer()["usersApi"];
        $usersApi->getAclManager()->disableAccessCheck();
        $user = $usersApi->findOne(["email" => $email, "active" => true]);
        if ($user->isEmpty()) {
            return null;
        }
        $usersApi->getAclManager()->enableAccessCheck();
        $custodian->sessionStart($user->get());
        $redirectUrl = $this->getRequest()->getParam("r", $this->getRouteUrl("root"));
        $this->redirectToUrl($redirectUrl);
    }

    public function logoutAction()
    {
        $this->autoRenderOff();
        /** @var Custodian $custodian */
        $custodian = $this->getDiContainer()["custodian"];
        $custodian->sessionClose();
        $this->redirect("root");
    }

}

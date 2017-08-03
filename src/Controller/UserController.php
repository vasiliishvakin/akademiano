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
            return false;
        }
        /** @var Custodian $custodian */
        $custodian = $this->getDiContainer()["custodian"];
        try {
            $auth = $custodian->authenticate($email, $password);
        } catch (EmptyException $e) {
            return false;
        }
        if (!$auth) {
            return false;
        }
        /** @var UsersApi $usersApi */
        $usersApi = $this->getDiContainer()["usersApi"];
        $usersApi->disabledAccessCheck();
        $user = $usersApi->findOne(["email" => $email, "active" => true]);
        $usersApi->enableAcessCheck();
        if ($user->isEmpty()) {
            return false;
        }
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

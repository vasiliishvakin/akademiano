<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace User\Controller;

use Acl\Model\Parts\AclController;
use DeltaCore\Exception\AccessDeniedException;
use DeltaRouter\Exception\NotFoundException;
use User\Model\UserManager;
use DeltaCore\AbstractController;

class UserController extends AbstractController
{
    use AclController;

    public function loginAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $app = $this->getApplication();
            /** @var UserManager $userManager */
            $userManager = $app['userManager'];
            $email = $this->getRequest()->getParam('email');
            $password = $this->getRequest()->getParam('password');
            $user = $userManager->authenticate($email, $password);
            if (!$user) {
                $this->autoRenderOff();
                return $this->getResponse()->redirect("/login");
            }
            $userManager->setCurrentUser($user);
            $this->getResponse()->redirect("/user");
        }
    }

    public function logoutAction()
    {
        $app = $this->getApplication();
        /** @var UserManager $userManager */
        $userManager = $app['userManager'];
        $userManager->logout();
        $this->getResponse()->redirect("/");
    }

    public function userAction()
    {
        $request = $this->getRequest();
        $userId = $request->getUriPartByNum(2);
        /** @var UserManager $userManager */
        $userManager = $this->getApplication()['userManager'];
        $user = ($userId) ? $userManager->findById($userId) : $userManager->getCurrentUser();
        if (!$user) {
            throw new NotFoundException('user not defined');
        }
        if (!$this->isAllow()) {
            throw new AccessDeniedException();
        }
        $this->getView()->assign('user', $user);
    }

}
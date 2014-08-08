<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace User\Controller;

use Acl\Model\Parts\AclController;
use DeltaCore\Exception\AccessDeniedException;
use DeltaRouter\Exception\NotFoundException;
use PermAuth\Model\Authenticator;
use PermAuth\Model\TokenManager;
use User\Model\UserManager;
use DeltaCore\AbstractController;

class UserController extends AbstractController
{
    use AclController;

    /**
     * @return Authenticator|null
     */
    public function getPermAuthenticator()
    {
        $application = $this->getApplication();
        if (isset($application["permAuthenticator"])) {
            return $application["permAuthenticator"];
        }
        return null;
    }

    public function loginAction()
    {
        $app = $this->getApplication();
        /** @var UserManager $userManager */
        $userManager = $app['userManager'];
        $permManager = $this->getPermAuthenticator();
        $isPermAuthOption = !is_null($permManager);
        $this->getView()->assign("isPermAuthOption", $isPermAuthOption);
        //auth by perm
        if ($isPermAuthOption) {
            $user = $permManager->authenticate();
            if ($user) {
                return $this->processLoginResult($user, true);
            }
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $email = $request->getParam('email');
            $password = $request->getParam('password');
            $remember = $request->getParam("remember", false);
            $user = $userManager->authenticate($email, $password);
            return $this->processLoginResult($user, $remember);
        }
    }

    public function processLoginResult($user = null, $remember = false)
    {
        if (!$user) {
            $this->autoRenderOff();
            return $this->getResponse()->redirect("/login");
        }
        $app = $this->getApplication();
        /** @var UserManager $userManager */
        $userManager = $app['userManager'];
        $userManager->setCurrentUser($user);

        if ($remember) {
            $permManager = $this->getPermAuthenticator();
            if ($permManager) {
                $permManager->setToken($user);
            }
        }
        return $this->getResponse()->redirect("/user");
    }

    public function logoutAction()
    {
        $app = $this->getApplication();
        $permManager = $this->getPermAuthenticator();
        if ($permManager) {
            $permManager->logout();
        }
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
        /*if (!$this->isAllow()) {
            throw new AccessDeniedException();
        }*/
        $this->getView()->assign('user', $user);
    }

}
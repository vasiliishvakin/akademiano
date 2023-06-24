<?php

namespace Akademiano\UserEO\Controller;

use Akademiano\Core\Controller\AkademianoController;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\HttpWarp\Url\Query;
use Akademiano\User\CustodianIncludeInterface;
use Akademiano\User\CustodianIncludeTrait;
use Akademiano\UserEO\Api\v1\UsersApi;
use Akademiano\UserEO\Custodian;
use Akademiano\UserEO\Model\User;
use Akademiano\Utils\Exception\EmptyException;
use Akademiano\UserEO\UsersOpsRoutesStore as UsersRoutes;

class UserController extends AkademianoController implements CustodianIncludeInterface
{
    use CustodianIncludeTrait;

    public function getUserApi(): UsersApi
    {
        return $this->getDiContainer()["usersApi"];
    }

    public function loginAction()
    {
        $email = $this->getRequest()->getParam("email");
        $password = $this->getRequest()->getParam("password");
        $formParams = [];
        $redirectUrl = $this->getRequest()->getParam("r");
        if ($redirectUrl) {
            $formParams["redirectUrl"] = $redirectUrl;
        }

        if (empty($email) || empty($password)) {
            return $formParams;
        }
        /** @var Custodian $custodian */
        $custodian = $this->getDiContainer()["custodian"];
        try {
            $auth = $custodian->authenticate($email, $password);
        } catch (EmptyException $e) {
            return $formParams;
        }
        if (!$auth) {
            return $formParams;
        }
        $usersApi = $this->getUserApi();
        $usersApi->getAclManager()->disableAccessCheck();
        $user = $usersApi->findOne(["email" => $email, "active" => true]);
        if ($user->isEmpty()) {
            return $formParams;
        }
        $usersApi->getAclManager()->enableAccessCheck();
        $custodian->sessionStart($user->get());
        if (!$redirectUrl) {
            $redirectUrl = $this->getRouteUrl("root");
        }
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

    public function profileAction(array $params = [])
    {
        if (!isset($params["id"])) {
            if (!$this->isAuthenticate()) {
                $route = $this->getRouteUrl(UsersRoutes::LOGIN_ROUTE);
                $route->setQuery(new Query(
                    ["r" => $this->getRouteUrl(UsersRoutes::PROFILE_ROUTE)->getUrl()]
                ));
                $this->redirectToUrl($route);
            }
            $user = $this->getCurrentUser();
            $isSelf = true;
        } else {
            $id = hexdec($params["id"]);
            /** @var User $user */
            $user = $this->getUserApi()->get($id)->getOrThrow(new NotFoundException(sprintf('User with id %s not exist', $params["id"])));
            $isSelf = $user->getInt() === $this->getCurrentUser()->getInt();
        }

        return [
            "isSelf" => $isSelf,
            "user" => $user,
        ];
    }
}

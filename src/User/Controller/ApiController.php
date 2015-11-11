<?php
/**
 * User: Evgeniy
 */

namespace User\Controller;


use DeltaCore\AbstractController;
use User\Controller\Parts\UserManagerGetter;
use User\Model\UserPlace;
use User\Model\UserPlacesManager;

class ApiController extends AbstractController {

    use UserManagerGetter;


    public function IndexAction($params)
    {
        $this->autoRenderOff();
        try {
            $request = $this->getRequest();
            $action = isset($params["action"]) ? $params["action"] : "default";
            $method = $action . ucfirst(strtolower($request->getMethod())) . 'Action';
            if (!method_exists($this, $method)) {
                throw new \Exception('Unsupported action');
            }
            $result = $this->$method();
        } catch (\Exception $e) {
            $this->getResponse()->setCode(400);
            $this->getResponse()->sendHeaders();
            $result = $e->getMessage();
            $this->getResponse()->setCode(400);
        }
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function defaultPostAction()
    {
        $user = $this->getUserManager()->getCurrentUser();
        if (!$user->getId()) {
            $user = false;
            $application = $this->getApplication();
            if (isset($application["permAuthenticator"])) {
                $authenticator = $application["permAuthenticator"];
                $user = $authenticator->authenticate();
                if ($user) {
                    $this->getUserManager()->setCurrentUser($user);
                }
            }
        }
        if ($user) {
            $result = [
                'id' => $user->getId(),
                'ava40' => $user->getAvatar() ? $user->getAvatar()->getUri("40x40") : '/s/img/tourist.png',
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ];
        } else {
            $result = null;
        }
        return $result;
    }

    public function defaultGetAction()
    {
        return $this->defaultPostAction();
    }

}
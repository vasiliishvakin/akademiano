<?php
/**
 * User: Evgeniy
 */

namespace User\Controller;


use DeltaCore\AbstractController;
use User\Controller\Parts\UserManagerGetter;

class ApiController extends AbstractController {

    use UserManagerGetter;


    public function IndexAction()
    {
        $this->autoRenderOff();
        $result = false;
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $result = $this->userData();
            }
        } catch (\Exception $e) {
            $this->getResponse()->setCode(400);
            $this->getResponse()->sendHeaders();
            $result = $e->getMessage();
            $this->getResponse()->setCode(400);
        }
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function userData()
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
    
}
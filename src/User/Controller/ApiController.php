<?php
/**
 * User: Evgeniy
 */

namespace User\Controller;


use DeltaCore\AbstractController;
use DeltaCore\Exception\AccessDeniedException;
use DeltaRouter\Exception\NotFoundException;
use User\Controller\Parts\UserManagerGetter;
use User\Model\UserPlace;
use User\Model\UserPlacesManager;

class ApiController extends AbstractController {

    use UserManagerGetter;


    public function IndexAction()
    {
        $this->autoRenderOff();
        try {
            $request = $this->getRequest();
            $action = $request->getUriPartByNum(3);
            if (!$action) {
                $action = 'Default';
            }
            $method = $action . ucfirst($request->getMethod()) . 'Action';
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

    public function getPlacesGetAction()
    {
        $user = $this->getUserManager()->getCurrentUser();
        if (!$user->getId()) {
            throw new AccessDeniedException('Not Allowed');
        }
        /** @var UserPlacesManager $userPlacesManager */
        $userPlacesManager = $this->getApplication()['userPlacesManager'];
        /** @var UserPlace[] $places */
        $places = $userPlacesManager->find(['user' => $user->getId()]);
        $result = [];
        foreach ($places as $place) {
            $arr = [
                'id' => $place->getId(),
            ];
            $arr += $place->getData()->getData();
            $result[] = $arr;
        }
        return $result;
    }

    public function addPlacePostAction()
    {
        $user = $this->getUserManager()->getCurrentUser();
        if (!$user->getId()) {
            throw new AccessDeniedException('Not Allowed');
        }
        $data = json_decode(file_get_contents('php://input'), true);
        /** @var UserPlacesManager $userPlacesManager */
        $userPlacesManager = $this->getApplication()['userPlacesManager'];
        /** @var UserPlace $places */
        $place = $userPlacesManager->create();
        $place->setUser($user);
        $place->setData($data);
        $userPlacesManager->save($place);
        $result = [
            'id' => $place->getId(),
        ];
        $result += $place->getData()->getData();
        return $result;
    }

    public function editPlacePostAction()
    {
        $user = $this->getUserManager()->getCurrentUser();
        if (!$user->getId()) {
            throw new AccessDeniedException('Not Allowed');
        }
        $data = json_decode(file_get_contents('php://input'), true);
        /** @var UserPlacesManager $userPlacesManager */
        $userPlacesManager = $this->getApplication()['userPlacesManager'];
        /** @var UserPlace $places */
        $place = $userPlacesManager->findOne(['id' => $data['id'], 'user' => $user->getId()]);
        if (!$place) {
            throw new NotFoundException('Place is not found');
        }
        $place->setUser($user);
        $place->setData($data);
        $userPlacesManager->save($place);
        $result = [
            'id' => $place->getId(),
        ];
        $result += $place->getData()->getData();
        return $result;
    }

}
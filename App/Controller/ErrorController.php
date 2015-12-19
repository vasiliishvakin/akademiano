<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Controller;


use DeltaCore\AbstractController;
use User\Model\UserManager;

class ErrorController  extends AbstractController
{
    public function notFoundAction()
    {
        $this->getResponse()->setCode(404);
    }

    public function accessDeniedAction()
    {
        $this->getResponse()->setCode(403);
        /** @var UserManager $userManager */
        $userManager = $this->getApplication()['userManager'];
        $user = $userManager->getCurrentUser();
        if (!$user) {
            return $this->redirect("/login");
        }
        $this->getView()->assign("user", $user);
    }
} 
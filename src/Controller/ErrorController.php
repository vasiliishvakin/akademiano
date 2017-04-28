<?php

namespace Akademiano\App\Controller;


use Akademiano\Core\ApplicationController;
use Akademiano\User\AuthInterface;

class ErrorController  extends ApplicationController
{
    public function notFoundAction()
    {
        $this->getResponse()->setCode(404);
    }

    public function accessDeniedAction()
    {
        $this->getResponse()->setCode(403);
        /** @var AuthInterface $userManager */
        $userManager = $this->getDIContainer()['custodian'];
        $user = $userManager->getCurrentUser();
        if (!$user) {
            return $this->redirect("/login");
        }
        $this->getView()->assign("user", $user);
    }
}

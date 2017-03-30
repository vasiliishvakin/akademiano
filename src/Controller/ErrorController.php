<?php

namespace Akademiano\Acl\Controller;


use Akademiano\Acl\RestrictedControllerInterface;
use Akademiano\Core\Controller\AbstractController;

class ErrorController extends AbstractController implements RestrictedControllerInterface
{
    public function checkAccess()
    {
        return true;
    }

    public function accessDeniedAction()
    {
        $this->getResponse()->setCode(403);
    }
}

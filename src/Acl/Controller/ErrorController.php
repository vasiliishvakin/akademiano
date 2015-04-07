<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Acl\Controller;


use DeltaCore\AbstractController;

class ErrorController extends AbstractController
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
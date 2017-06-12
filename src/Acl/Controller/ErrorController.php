<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Acl\Controller;


use DeltaCore\AbstractController;
use DeltaCore\ActionsAccessAllowedInterface;
use DeltaCore\ActionsAccessAllowedTrait;

class ErrorController extends AbstractController implements ActionsAccessAllowedInterface
{
    use ActionsAccessAllowedTrait;

    public function __construct()
    {
        $this->setActionsAccessAllowed(["accessDenied"]);
    }

    public function accessDeniedAction()
    {
        $this->getResponse()->setCode(403);
    }
} 
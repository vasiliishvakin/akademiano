<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace App\Controller;

use DeltaCore\AbstractController;

class IndexController extends AbstractController
{
    public function indexAction()
    {
        $this->getView()->assign('canonicalRef', '/');
    }

} 
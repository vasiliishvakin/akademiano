<?php

namespace Akademiano\App\Controller;

use Akademiano\Core\AbstractController;

class IndexController extends AbstractController
{
    public function indexAction()
    {
        $this->getView()->assign('canonicalRef', '/');
    }

}

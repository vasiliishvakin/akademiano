<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Controller;

use DeltaCore\AbstractController;

class DeltaController extends AbstractController
{
    public function IndexAction()
    {
        $this->getView()->assign('canonicalRef', '/deltaphp');
    }

} 
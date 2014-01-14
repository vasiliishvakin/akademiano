<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

use DeltaCore\AbstractController;

class DeltaController extends AbstractController
{
    public function IndexAction()
    {
        $this->getView()->assign('canonicalRef', '/deltaphp');
    }

} 
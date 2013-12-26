<?php

use DeltaCore\AbstractController;

class IndexController extends AbstractController
{
    public function IndexAction()
    {
        $this->getView()->assign("assignedVar", "Index Controller [Delta]");

//        $this->getResponse()->setModified(1387690174);
//        $this->getResponse()->setTimeToCache("1d");
    }

}
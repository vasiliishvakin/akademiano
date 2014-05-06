<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Articles\Controller;


use Articles\Model\Parts\GetArticlesManager;
use DeltaCore\AbstractController;
use DeltaUtils\ArrayUtils;

class ApiController extends AbstractController
{
    use GetArticlesManager;

    public function DatesAction()
    {
        $this->autoRenderOff();
        $manager = $this->getArticlesManager();
        $dates = $manager->getDates();
        $dates = ArrayUtils::filterNulls($dates);
        $dates = array_values($dates);
        $dates = json_encode($dates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo $dates;
    }

} 
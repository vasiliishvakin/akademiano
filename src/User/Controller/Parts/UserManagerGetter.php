<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace User\Controller\Parts;


use DeltaCore\Application;
use User\Model\UserManager;

trait UserManagerGetter
{
    /**
     * @return Application
     */
    abstract public function getApplication();

    /**
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->getApplication()["userManager"];
    }

} 
<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    'userManager'      => function ($c) {
            $userManager = new \User\Model\UserManager();
            $userManager->setSession($c['sessions']);
            return $userManager;
        }
];
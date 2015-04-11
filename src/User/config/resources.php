<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    'userManager' => function ($c) {
        $userManager = new \User\Model\UserManager();
        $userManager->setSession($c['sessions']);
        $userManager->setFileManager($c["fileManager"]);
        $gm = $c["groupManager"];
        $userManager->setGroupManager($gm);
        return $userManager;
    },
    "groupManager" => function($c) {
        $gm = $c["directoryFactory"]->getManager("groups");
        return $gm;
    }
];
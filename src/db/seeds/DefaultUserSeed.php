<?php

use Phinx\Seed\AbstractSeed;

class DefaultUserSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        /** @var \Akademiano\Core\Application $app */
        $app = include __DIR__ . "/../../vendor/akademiano/core/src/bootstrap.php";
        /** @var \Akademiano\Config\ConfigLoader $configLoader */
        $configLoader = $app->getDiContainer()["baseConfigLoader"];
        $configLoader->addConfigDir(__DIR__ . '/../../src/config', PHP_INT_MAX);
        $app->init();

        /** @var \Akademiano\UserEO\Api\v1\GroupsApi $groupsApi */
        $groupsApi = $app->getDiContainer()["groupsApi"];
        $group = $groupsApi->find(["title" => DefaultGroupSeed::DEFAULT_ADMIN_GROUP_TITLE])->getItems()->firstOrFail(new \Akademiano\Utils\Exception\EmptyException(
            sprintf('Default group "%s" not found', DefaultGroupSeed::DEFAULT_ADMIN_GROUP_TITLE)
        ));

        /** @var \Akademiano\UserEO\Api\v1\UsersApi $usersApi */
        $usersApi = $app->getDiContainer()["usersApi"];
        $usersApi->save([
            "title" => "admin",
            "email" => "admin@example.com",
            "newPassword" => "admin",
            "group" => $group,
        ]);


        $groupsApi = $app->getDiContainer()["groupsApi"];
        $group = $groupsApi->find(["title" => DefaultGroupSeed::DEFAULT_USER_GROUP_TITLE])->getItems()->firstOrFail(new \Akademiano\Utils\Exception\EmptyException(
            sprintf('Default group "%s" not found', DefaultGroupSeed::DEFAULT_USER_GROUP_TITLE)
        ));

        /** @var \Akademiano\UserEO\Api\v1\UsersApi $usersApi */
        $usersApi = $app->getDiContainer()["usersApi"];
        $usersApi->save([
            "title" => "user",
            "email" => "user@example.com",
            "newPassword" => "user",
            "group" => $group,
        ]);
    }
}

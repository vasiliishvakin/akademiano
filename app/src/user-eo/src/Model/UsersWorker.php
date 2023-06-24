<?php


namespace Akademiano\UserEO\Model;

use Akademiano\EntityOperator\Worker\NamedEntitiesWorker;

class UsersWorker extends NamedEntitiesWorker
{
    const WORKER_ID = 'usersWorker';
    const TABLE_NAME = 'users';
    const FIELDS = ['email', 'password', 'group', 'phone'];
    const EXT_ENTITY_FIELDS = ['group'];

    public const ENTITY = User::class;
}

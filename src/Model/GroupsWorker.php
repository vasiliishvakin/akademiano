<?php

namespace Akademiano\UserEO\Model;


use Akademiano\EntityOperator\Worker\NamedEntitiesWorker;

class GroupsWorker extends NamedEntitiesWorker
{
    const WORKER_ID = 'groupsWorker';
    const TABLE_ID = 10;
    const TABLE_NAME = 'groups';

    public static function getEntityClassForMapFilter()
    {
        return Group::class;
    }
}

<?php

namespace Akademiano\UserEO\Model;


use Akademiano\EntityOperator\Worker\PostgresWorker;

class GroupsWorker extends PostgresWorker
{
    const TABLE_ID = 10;
    const TABLE_NAME = "groups";
    const EXPAND_FIELDS = ["title", "description"];
}

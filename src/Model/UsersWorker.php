<?php


namespace Akademiano\UserEO\Model;

use Akademiano\EntityOperator\Worker\PostgresWorker;

class UsersWorker extends PostgresWorker
{
    const TABLE_ID = 11;
    const TABLE_NAME = "users";
    const EXPAND_FIELDS = ["title", "description", "email", "phone", "password", "group"];

}

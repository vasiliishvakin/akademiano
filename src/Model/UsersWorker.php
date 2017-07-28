<?php


namespace Akademiano\UserEO\Model;


use Akademiano\EntityOperator\Command\DeleteCommand;
use Akademiano\Operator\Command\CommandInterface;
use Akademiano\Operator\Command\PreCommandInterface;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;
use Akademiano\EntityOperator\Worker\PostgresWorker;

class UsersWorker extends PostgresWorker implements DelegatingInterface
{
    const TABLE_ID = 11;
    const TABLE_NAME = "users";
    const EXPAND_FIELDS = ["title", "description", "email", "password", "group"];

    use DelegatingTrait;
}

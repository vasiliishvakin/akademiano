<?php

namespace Akademiano\Content\Entries\Model;
use Akademiano\EntityOperator\Worker\PostgresWorker;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;

class EntriesWorker extends PostgresWorker implements DelegatingInterface
{
    const TABLE_ID_INC = 1;
    const TABLE_ID = 3 + self::TABLE_ID_INC;
    const TABLE_NAME = "entries";
    const EXPAND_FIELDS = ["title", "description"];

    use DelegatingTrait;
}

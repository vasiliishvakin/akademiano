<?php

namespace Akademiano\Content\Countries\Model;
use Akademiano\EntityOperator\Worker\PostgresWorker;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;

class CountriesWorker extends PostgresWorker
{
    const TABLE_ID = 19;
    const TABLE_NAME = "countries";
    const EXPAND_FIELDS = ["title", "description"];
}

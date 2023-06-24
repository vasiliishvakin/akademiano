<?php

namespace Akademiano\Content\Countries\Model;
use Akademiano\EntityOperator\Worker\NamedEntitiesWorker;

class CountriesWorker extends NamedEntitiesWorker
{
    const WORKER_ID = 'countriesWorker';
    const TABLE_ID = 22;
    const TABLE_NAME = "countries";

    public static function getEntityClassForMapFilter()
    {
        return Country::class;
    }
}

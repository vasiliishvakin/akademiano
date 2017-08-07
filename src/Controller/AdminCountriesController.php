<?php

namespace Visa\Tasks\Countries\Controller;


use Akademiano\Content\Countries\Api\v1\CountriesApi;
use Akademiano\Content\Countries\CountriesOpsRoutesStore;
use Akademiano\EntityOperator\Ext\Controller\AkademianoEntityController;

class AdminCountriesController extends AkademianoEntityController
{
    const ENTITY_OPSR_STORE_CLASS = CountriesOpsRoutesStore::class;
    const ENTITY_API_ID = CountriesApi::API_ID;

    public function getListCriteria()
    {
        return [];
    }
}

<?php


namespace Akademiano\Content\Countries\Api\v1;


use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\Content\Comments\Api\v1\CommentsApi;
use Akademiano\Content\Countries\Model\Country;
use Akademiano\Core\Exception\AccessDeniedException;
use Akademiano\Entity\EntityInterface;
use Datamapper\Content\Tasks\Model\Task;

class CountriesApi extends EntityApi
{
    const ENTITY_CLASS = Country::class;
    const API_ID = "countriesApi";
}

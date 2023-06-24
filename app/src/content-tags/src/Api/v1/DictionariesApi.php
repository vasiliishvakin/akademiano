<?php


namespace Akademiano\Content\Tags\Api\v1;


use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\Content\Tags\Model\Dictionary;

class DictionariesApi extends EntityApi
{
    const ENTITY_CLASS = Dictionary::class;
    const API_ID = "dictionariesApi";
}

<?php


namespace Akademiano\UserEO\Api\v1;


use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\UserEO\Model\Group;

class GroupsApi extends EntityApi
{
    const ENTITY_CLASS = Group::class;
    const API_ID = "groupsApi";
}

<?php

namespace Akademiano\EntityOperator\Ext\Controller;

use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\Api\v1\Entities\EntityApiInterface;
use Akademiano\Core\Controller\AkademianoController;
use Akademiano\Router\RoutePattern;

/**
 * Class AkademianoEntityController
 * @package Akademiano\EntityOperator\Ext\Controller
 * CRUDL functions for entities
 */
abstract class AkademianoEntityController extends AkademianoController
{
    public const ENTITY_API_ID = EntityApi::API_ID;

    public function getEntityApi(): EntityApiInterface
    {
        return $this->getDiContainer()[static::ENTITY_API_ID];
    }
}

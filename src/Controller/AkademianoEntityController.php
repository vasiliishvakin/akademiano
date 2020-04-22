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
    public const ROUTES_PARAMS = null;

    public function getEntityApi(): EntityApiInterface
    {
        return $this->getDiContainer()[static::ENTITY_API_ID];
    }

    protected static function buildRouteData(string $controllerId, string $patternValue, string $actionId, int $patternType = RoutePattern::TYPE_DEFAULT, ?array $valueParams = null): array
    {
        if (empty($valueParams)) {
            $valueParams = static::ROUTES_PARAMS;
        }
        if (!empty($valueParams)) {
            $patternValue = vsprintf($patternValue, $valueParams);
        }

        return [
            "patterns" => [
                "type" => $patternType,
                "value" => $patternValue,
            ],
            "action" => [$controllerId, $actionId],
        ];
    }
}

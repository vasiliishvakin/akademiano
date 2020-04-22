<?php


namespace Akademiano\EntityOperator\Ext\Controller;


use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\Utils\ClassTools;

abstract class AkademianoCompositeEntityController extends AkademianoEntityController
{
    const RELATIONS = [];

    public function getRelations(): ?array
    {
        return ClassTools::getClassTreeArrayConstant(get_class($this), 'RELATIONS');
    }

    public function getRelatedEntityApi(string $apiId): EntityApi
    {
        $apiGetMethod = 'get' . ucfirst($apiId);
        if (method_exists($this, $apiGetMethod)) {
            $api = $this->$apiGetMethod();
        } else {
            $api = $this->getDiContainer()[$apiId];
        }
        return $api;
    }
}

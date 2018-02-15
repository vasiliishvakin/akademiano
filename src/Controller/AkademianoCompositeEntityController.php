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

    public function formAction(array $params = [])
    {
        $data = parent::formAction($params);

        $relations = $this->getRelations();
        foreach ($relations as $relationId=>$relationApiId) {
            $currentRelationsGetMethod = 'get' . ucfirst($relationId) . 'FormList';
            if (method_exists($this, $currentRelationsGetMethod)) {
                $relatedItems = $this->$currentRelationsGetMethod();
            } else {
                $api = $this->getRelatedEntityApi($relationApiId);
                $relatedItems = $api->find([]);
            }
            $data[$relationId] = $relatedItems;
        }
        return $data;
    }
}

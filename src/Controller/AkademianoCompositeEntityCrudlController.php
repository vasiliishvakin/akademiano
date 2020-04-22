<?php


namespace Akademiano\EntityOperator\Ext\Controller;


use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

class AkademianoCompositeEntityCrudlController extends AkademianoCompositeEntityController
{
    const ENTITY_OPSR_STORE_CLASS = EntityOpsRoutesStore::class;

    const DEFAULT_ITEMS_PER_PAGE = 20;
    const DEFAULT_LIST_CRITERIA = null;

    use EntityCrudlTrait {
        formAction as protected EntityCrudlFormAction;
    }

    public function formAction(array $params = [])
    {
        $data = $this->EntityCrudlFormAction($params);

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

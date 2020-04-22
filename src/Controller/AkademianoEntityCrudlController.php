<?php


namespace Akademiano\EntityOperator\Ext\Controller;


use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;

class AkademianoEntityCrudlController extends AkademianoEntityController
{
    const ENTITY_OPSR_STORE_CLASS = EntityOpsRoutesStore::class;

    const DEFAULT_ITEMS_PER_PAGE = 20;
    const DEFAULT_LIST_CRITERIA = null;

    use EntityCrudlTrait;
}

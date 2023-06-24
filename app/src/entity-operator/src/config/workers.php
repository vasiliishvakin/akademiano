<?php

return [
    \Akademiano\EntityOperator\Worker\EntityCreatorWorker::class,
    \Akademiano\EntityOperator\Worker\EntitiesWorker::class,
    \Akademiano\EntityOperator\Worker\TranslatorDataToObjectEntityWorker::class,
    \Akademiano\EntityOperator\Worker\TranslatorObjectToDataEntityWorker::class,
    \Akademiano\EntityOperator\Worker\SetEntityExistingEntityWorker::class,
    \Akademiano\EntityOperator\Worker\RelationsWorker::class,
    \Akademiano\EntityOperator\Worker\TablesIdsWorker::class,
];

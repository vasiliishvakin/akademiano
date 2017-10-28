<?php

return [
    "operator" => function ($c) {
        $e = new \Akademiano\EntityOperator\EntityOperator();
        $e->setDependencies($c);
        /** @var \Akademiano\Config\ConfigLoader $configLoader */
        $configLoader = $c['configLoader'];
        $classMap = $configLoader->getConfig(\Akademiano\EntityOperator\EntityOperator::CLAS_MAP_FILE_NAME);
        $e->setClassMap($classMap);
        return $e;
    },
];

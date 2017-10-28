<?php

return [
    "operator" => function ($c) {
        $e = new \Akademiano\Operator\Operator();
        $e->setDependencies($c);
        /** @var \Akademiano\Config\ConfigLoader $configLoader */
        $configLoader = $c['configLoader'];
        $classMap = $configLoader->getConfig(\Akademiano\Operator\Operator::CLAS_MAP_FILE_NAME);
        $e->setClassMap($classMap);
        return $e;
    },
];

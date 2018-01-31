<?php

return [
    \Akademiano\Delegating\OperatorInterface::RESOURCE_ID => function (\Pimple\Container $c) {
        $o = new \Akademiano\Operator\Operator($c);
        /** @var \Akademiano\Config\ConfigLoader $configLoader */
        $configLoader = $c['configLoader'];
        $workers = $configLoader->getConfig(\Akademiano\Operator\Operator::WORKERS_FILE);
        $o->setWorkers($workers);
        $relations = $configLoader->getConfig(\Akademiano\Operator\Operator::WORKERS_MAP_FILE);
        $o->getWorkersMap()->addRelations($relations);
        return $o;
    },
];

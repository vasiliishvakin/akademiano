<?php
return [
    "dbDefaultAdapterClosure" => function ($c) {
        return function () use ($c) {
            /** @var \DeltaCore\Config $config */
            $config = $c->getConfig();
            $dbAdapter = new \DeltaDb\Adapter\PgsqlAdapter();
            $dbAdapter->connect("host={$config->get(['database', 'default', 'host'], '127.0.0.1')} dbname={$config->get(['database', 'default', 'name'], 'test')} user={$config->get(['database', 'default', 'user'], 'postgres')} password={$config->get(['database', 'default', 'password'], 'postgres')}");
            return $dbAdapter;
        };
    },
    "relationsFactory" => function ($c) {
        $factory = new \DeltaDb\Model\Relations\relationsFactory();
        $relations = $c->getConfig()->get(["DeltaDb", "relations"]);
        if ($relations) {
            $relations = $relations->toArray();
        }
        foreach($relations as $name=>$params) {
            $managerFirst = $params[0];
            $managerSecond = $params[1];
            $factory->setManagerParams(
              $name,
              function() use ($c, $managerFirst) {return $c[$managerFirst];},
              function() use ($c, $managerSecond) {return $c[$managerSecond];}
            );
        }

        return $factory;
    }
];
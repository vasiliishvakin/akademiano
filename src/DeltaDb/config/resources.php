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
];
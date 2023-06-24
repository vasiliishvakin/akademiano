<?php

use \Akademiano\Db\Adapter\PgsqlAdapter;

return [
    PgsqlAdapter::RESOURCE_ID => function ($c) {
        /** @var \Akademiano\Config\Config $config */
        $config = $c["config"];
        $dbAdapter = new \Akademiano\Db\Adapter\PgsqlAdapter();
        $dbAdapter->setDsn(sprintf('host=%s dbname=%s user=%s password=%s',
            $config->get(['database', 'default', 'host'], '127.0.0.1'),
            $config->get(['database', 'default', 'name'], 'test'),
            $config->get(['database', 'default', 'user'], 'postgres'),
            $config->get(['database', 'default', 'password'], 'postgres')
        ));
        if (isset($c[PgsqlAdapter::FILTER_VALUE_RESOURCE_ID])) {
            $dbAdapter->setFilterValue($c[PgsqlAdapter::FILTER_VALUE_RESOURCE_ID]);
        }
        return $dbAdapter;
    }
];

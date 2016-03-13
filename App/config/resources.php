<?php
return [
    "environment" => function($c) {
        /** @var \DeltaCore\Application $c */
        $config = $c->getConfig("environment", null);
        $env = new \HttpWarp\Environment();
        if ($config instanceof \DeltaCore\Config) {
            $config->getAndCall("serverName", [$env, "setServerName"]);
            $config->getAndCall("port", [$env, "setPort"]);
            $config->getAndCall("https", [$env, "setHttps"]);
        }
       return $env;
    }
];

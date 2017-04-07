<?php

if (!defined("ROOT_DIR")) {
    define('ROOT_DIR', realpath(__DIR__ . '/..'));
}
if (!defined("PUBLIC_DIR")) {
    define('PUBLIC_DIR', ROOT_DIR . '/public');
}
if (!defined("VENDOR_DIR")) {
    define('VENDOR_DIR', ROOT_DIR . '/vendor');
}
if (!defined("DATA_DIR")) {
    define('DATA_DIR', ROOT_DIR . '/data');
}

if (!is_dir(__DIR__ . "/../vendor")) {
    die("Access Denied: index script not inside root public dir");
}

$loader = require ROOT_DIR . "/vendor/autoload.php";

$app = new \Akademiano\Core\Application();
$app->setLoader($loader);

$app->getDiContainer()->extend('baseConfigLoader', function (\Akademiano\Config\ConfigLoader $configLoader, \Pimple\Container $pimple) {
    $configLoader->addConfigDir(ROOT_DIR . "/src/config");
    return $configLoader;
});

$app->run();

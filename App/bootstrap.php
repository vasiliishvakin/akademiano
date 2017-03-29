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

$loader = require ROOT_DIR . "/vendor/autoload.php";

$app = new \Akademiano\Core\Application();
$app->setLoader($loader);

return $app;

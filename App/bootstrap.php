<?php

define('ROOT_DIR', realpath(__DIR__ . '/..'));
define('PUBLIC_DIR', ROOT_DIR . '/public');
define('VENDOR_DIR', ROOT_DIR . '/vendor');
define('DATA_DIR', ROOT_DIR . '/data');

$loader = require ROOT_DIR . "/vendor/autoload.php";

$app = new \DeltaCore\Application();
$app->setLoader($loader);

return $app;

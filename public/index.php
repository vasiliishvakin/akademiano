<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

define('ROOT_DIR', realpath(__DIR__ . '/..')); // надо исключить realpath из кода и вынести в конфиг
define('PUBLIC_DIR', ROOT_DIR . '/public');
define('VENDOR_DIR', ROOT_DIR . '/vendor');
define('DATA_DIR', ROOT_DIR . '/data');

$loader = include_once ROOT_DIR . "/vendor/autoload.php";

$app = new \DeltaCore\Application();
$app->setLoader($loader);

$app->run();
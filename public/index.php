<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

define('ROOT_DIR', realpath(__DIR__ . '/..'));
define('PUBLIC_DIR', ROOT_DIR . '/public');
define('DATA_DIR', ROOT_DIR . '/data');

set_include_path(
ROOT_DIR . '/controller'
. PATH_SEPARATOR  .
get_include_path());

$loader = include_once ROOT_DIR . "/vendor/autoload.php";
$loader->setUseIncludePath(true);

use DeltaCore\Application;

$app = new Application();

$app->run();
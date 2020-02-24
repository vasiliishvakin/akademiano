<?php

use Akademiano\Core\ApplicationFactory;

if (!defined("ROOT_DIR")) {
    define('ROOT_DIR', dirname(__DIR__, 4));
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

require_once __DIR__ . '/ApplicationFactory.php';

return ApplicationFactory::factory();

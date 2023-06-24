<?php
if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    define('C3_CODECOVERAGE_ERROR_LOG_FILE', __DIR__."/data/logs/c3_error.log");

    include __DIR__ . '/c3.php';

    include __DIR__ . '/public/index.php';
}

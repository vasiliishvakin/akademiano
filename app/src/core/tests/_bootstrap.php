<?php
// This is global bootstrap for autoloading
if (!defined('ROOT_DIR')) {
    $rootDir = realpath(__DIR__ . '/../');
    define('ROOT_DIR', $rootDir);
}
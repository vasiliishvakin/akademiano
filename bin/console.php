#!/usr/bin/env php
<?php

use Webmozart\Console\ConsoleApplication;

$app = require_once __DIR__ . "/../App/bootstrap.php";

$config = new \Akademiano\Core\ConsoleConfig($app);

$cli = new ConsoleApplication($config);
$cli->run();

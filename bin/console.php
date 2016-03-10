#!/usr/bin/env php
<?php

use Webmozart\Console\ConsoleApplication;

$app = require_once __DIR__ . "/../App/bootstrap.php";

$config = new \DeltaCore\ConsoleConfig($app);

$cli = new ConsoleApplication($config);
$cli->run();

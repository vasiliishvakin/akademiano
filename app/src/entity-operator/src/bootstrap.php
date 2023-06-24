<?php
/** @var \Akademiano\Core\Application $app */
$app = include dirname(__DIR__, 2) . '/core/src/bootstrap.php';
$app->init();
/** @var \Akademiano\Operator\Operator $operator */
$operator = $app->getDiContainer()[Akademiano\Operator\Operator::RESOURCE_ID];
return $operator;

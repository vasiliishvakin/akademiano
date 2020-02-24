<?php

namespace Akademiano\Core;

class ApplicationFactory
{
    /** @var Application */
    protected static $application;

    public static function factory(): Application
    {
        if (null === self::$application) {
            $loader = require dirname(__DIR__, 3 ). '/autoload.php';
            $app = new Application();
            $app->setLoader($loader);
            self::$application = $app;
        }
        return self::$application;
    }
}

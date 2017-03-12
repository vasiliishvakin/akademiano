<?php


namespace Akademiano\Utils;

use Akademiano\Utils\Exception\CaughtErrorException;

class CatchError
{
    /** @var  bool */
    protected static $started;

    /** @var  CaughtErrorException */
    protected static $error;


    public static function isStarted()
    {
        return static::$started;
    }

    public static function start($errorLevel = \E_WARNING)
    {
        if (static::isStarted()) {
            throw new Exception('CatchError already started');
        }

        static::$started        = true;
        static::$error = null;

        set_error_handler(array(get_called_class(), 'catchError'), $errorLevel);
    }

    public static function stop($throw = false)
    {
        if (!static::isStarted()) {
            throw new Exception('ErrorHandler not started');
        }

        $error = static::$error;

        static::$started        = false;
        static::$error = null;
        restore_error_handler();

        if ($error && $throw) {
            throw $error;
        }
        return $error;
    }

    public static function catchError($errno, $errstr = '', $errfile = '', $errline = 0)
    {
        static::$error = new CaughtErrorException($errstr, 0, $errno, $errfile, $errline, static::$error);
    }
}

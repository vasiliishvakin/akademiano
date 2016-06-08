<?php

namespace DeltaDb;

use Acl\Model\Adapter\AbstractAdapter;

class DbaStorage
{
    const DBA_DEFAULT = 'default';

    protected static $storage = [];

    public static function setDba($name, $callback, array $options = null)
    {
        $dba = compact('callback', 'options');
        self::$storage[$name] = $dba;
    }

    public static function setDefault($callback, array $options = null)
    {
        self::setDba('default', $callback, $options);
    }

    /**
     * @param string $name
     * @return \DeltaDb\Adapter\AdapterInterface
     * @throws \RuntimeException
     */
    public static function getDba($name = self::DBA_DEFAULT)
    {
        if (is_null($name)) {
            $name = self::DBA_DEFAULT;
        }

        $dba = null;

        if (isset(self::$storage[$name]) && is_object(self::$storage[$name])) {
            return self::$storage[$name];
        }

        if (isset(self::$storage[$name])) {
            $dba = self::$storage[$name];
        } else {
            throw new \RuntimeException("Dba with name $name not registerd in dba storage");
        }

        if (is_object($dba) && $dba instanceof AbstractAdapter) {
            self::$storage[$name] = $dba;
        } elseif (is_array($dba)) {
            if (!isset($dba['options']) || empty($dba['options'])) {
                $dba = call_user_func($dba['callback']);
            } else {
                $dba = call_user_func_array($dba['callback'], $dba['options']);
            }
            self::$storage[$name] = $dba;
        }
        return self::$storage[$name];
    }
}


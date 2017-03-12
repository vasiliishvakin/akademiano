<?php

namespace Akademiano\Utils;

class ArrayTools
{
    const FIRST_IN_ARRAY = '____first';
    const LAST_IN_ARRAY = '____last';
    const ARRAY_TYPE_ASSOC = 1;
    const ARRAY_TYPE_NUM = -1;
    const ARRAY_TYPE_COMB = 0;


    public static function mergeRecursive()
    {
        $arrays = func_get_args();
        $merged = array_shift($arrays);
        foreach ($arrays as $currentArray) {
            foreach ($currentArray as $key => $value) {
                if (is_array($value) && isset ($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = self::mergeRecursive($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }

    public static function mergeRecursiveDisabled()
    {
        $arrays = func_get_args();
        $merged = array_shift($arrays);
        foreach ($arrays as $currentArray) {
            foreach ($currentArray as $key => $value) {
                if (is_array($value) && isset ($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = self::mergeRecursiveDisabled($merged[$key], $value);
                } else {
                    //проверяем что это не отключение параметров
                    $unSetted = false;
                    if ($value === null) {
                        if (false !== $keyMerged = array_search($key, $merged)) {
                            if (is_integer($keyMerged)) {
                                unset($merged[$keyMerged]);
                                $unSetted = true;
                            }
                        }
                    }
                    if (!$unSetted) {
                        $merged[$key] = $value;
                    }
                }
            }
        }

        return $merged;
    }

    /**
     * @param array $array
     * @param array|string|null $path
     * @param $value
     * @return array
     */
    public static function set(array $array, $path = null, $value)
    {
        if (is_null($path)) {
            if (!is_array($value)) {
                $array[] = $value;
                return $array;
            } else {
                return array_merge($array, $value);
            }
        }
        $path = (array)$path;
        $current = &$array;
        foreach ($path as $item) {
            if (!isset($current[$item])) {
                $current[$item] = null;
            }
            $current = &$current[$item];
        }
        $current = $value;

        return $array;
    }


    /**
     * @param array $array
     * @param null $path
     * @param $value
     * @return array
     */
    public static function add(array $array, $path = null, $value)
    {
        if (is_null($path)) {
                $array[] = $value;
                return $array;
        }
        $path = (array)$path;
        $current = &$array;
        foreach ($path as $item) {
            if (!isset($current[$item])) {
                $current[$item] = null;
            }
            $current = &$current[$item];
        }
        $current[] = $value;

        return $array;
    }

    /**
     * @param array $array
     * @param array|string|null $path
     * @param $value
     * @return array
     * @deprecated
     */
    public static function setByPath(array $array, $path = null, $value)
    {
        return self::set($array, $path, $value);
    }

    public static function get($array, $path, $default = null)
    {
        if (is_null($path)) {
            return $array;
        }
        $path = (array)$path;
        $current = $array;
        foreach ($path as $item) {
            if (!isset($current[$item])) {
                return $default;
            }
            $current = $current[$item];
        }

        return $current;
    }

    public static function getMaybe(array $array, $path)
    {
        if (self::issetByPath($array, $path)) {
            return new \PhpOption\Some(self::get($array, $path));
        } else {
            return \PhpOption\None::create();
        }
    }

    /**
     * @param array $array
     * @param $path
     * @param \Callable|array $callback
     * @param array|null $arguments
     */
    public static function getAndCall(array $array, $path, $callback, array $arguments = null)
    {
        if (self::issetByPath($array, $path)) {
            $value = self::get($array, $path);
            if (null !== $arguments) {
                array_unshift($arguments, $value);
            } else {
                $arguments = [$value];
            }
            return call_user_func_array($callback, $arguments);
        }
    }

    /**
     * @param array $array
     * @param null $path
     * @param null $default
     * @return mixed
     * @deprecated
     */
    public static function getByPath(array $array, $path = null, $default = null)
    {
        return self::get($array, $path, $default);
    }

    public static function issetByPath(array $array, $path)
    {
        if (is_null($path)) {
            return true;
        }
        $path = (array)$path;

        $current = $array;
        foreach ($path as $item) {
            if (!isset($current[$item])) {
                return false;
            }
            $current = $current[$item];
        }

        return true;
    }

    public static function switchIn($value, array $arrayCases = [], $default = self::FIRST_IN_ARRAY)
    {
        $value = strtolower($value);
        foreach ($arrayCases as $case) {
            if ($value === strtolower($case)) {
                return $value;
            }
        }
        if (empty($arrayCases)) {
            switch ($default) {
                case self::FIRST_IN_ARRAY :
                case self::LAST_IN_ARRAY :
                    return null;
                default :
                    return $default;
            }
        }
        switch ($default) {
            case self::FIRST_IN_ARRAY :
                return $arrayCases[0];
                break;
            case self::LAST_IN_ARRAY :
                return (count($arrayCases) > 0) ? $arrayCases[count($arrayCases) - 1] : null;
                break;
            default :
                return $default;
        }
    }

    public static function sortByKey($array, $key = 'order')
    {
        usort($array, function ($a, $b) use ($key) {
            return $a[$key] - $b[$key];
        });

        return $array;
    }

    public static function getSlice($array, $path, $default = null)
    {
        $valArray = [];
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                $valArray[$key] = $default;
            } else {
                $valArray[$key] = self::get($value, $path, $default);
            }
        }

        return $valArray;
    }

    public static function filterNulls(array $array)
    {
        return array_filter($array, function ($var) {
            return !is_null($var);
        });
    }

    /**
     * @param array $array
     * @return bool
     * @deprecated use getArrayType with 3 value: assoc (1), num (-1), combined (0)
     */
    public static function isAssoc(array $array)
    {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

    /**
     * @param array $array
     * @return int assoc (1), num (-1), combined (0)
     */
    public static function getArrayType(array $array)
    {
        $keys = array_keys($array);
        $associative = false;
        $numeric = false;
        foreach ($keys as $key) {
            if (is_string($key)) {
                $associative = true;
            } else {
                $numeric = true;
            }
            if ($associative && $numeric) {
                return 0;
            }
        }

        return $associative ? 1 : -1;
    }

    public static function implodePairs($glue, $array, $operator = "=")
    {
        $preparedArray = [];
        foreach ($array as $key => $value) {
            $preparedArray[] = $key . $operator . $value;
        }

        return implode($glue, $preparedArray);
    }

    public static function implodeRecursive($glue = "", array $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::implodeRecursive($glue, $value);
            }
        }

        return implode($glue, $value);
    }

    public static function isMulti($a)
    {
        foreach ($a as $v) {
            if (is_array($v)) {
                return true;
            }
        }

        return false;
    }

    public static function renameKeys($array, Callable $function)
    {
        $keys = array_keys($array);
        $keys = array_map($function, $keys);
        $array = array_combine($keys, $array);

        return $array;
    }

    public static function remove($array, $path)
    {
        if (is_null($path)) {
            return $array;
        }
        $path = (array)$path;
        $key = array_shift($path);
        if (count($path) <= 0) {
            if (isset($array[$key])) {
                unset($array[$key]);
            }
            return $array;
        }
        if (!isset($array[$key]) || is_null($array[$key])) {
            return null;
        }
        if (is_array($array[$key])) {
            if (count($array[$key]) <= 0) {
                unset($array[$key]);
            } else {
                $array[$key] = self::remove($array[$key], $path);
            }
        }
        return $array;
    }

    public static function extract(&$array, $path, $default = null)
    {
        $value = self::get($array, $path, $default);
        $array = self::remove($array, $path);
        return $value;
    }

    public static function implode($glue, $data)
    {
        if (!is_array($data)) {
            return $data;
        }
        return implode($glue, $data);

    }
}

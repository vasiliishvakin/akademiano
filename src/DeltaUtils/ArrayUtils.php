<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaUtils;

class ArrayUtils {
    const FIRST_IN_ARRAY = '____first';
    const LAST_IN_ARRAY =  '____last';

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

    public static function setByPath(array $array, array $path, $value)
    {
        $current = &$array;
        foreach($path as $item) {
            if (!isset($current[$item])) {
                $current[$item] = null;
            }
            $current = &$current[$item];
        }
        $current = $value;
        return $array;
    }

    public static function getByPath(array $array, $path = null, $default = null)
    {
        if (is_null($path)) {
            return $array;
        }
        $path = (array) $path;

        $current = $array;
        foreach($path as $item) {
            if (!isset($current[$item])) {
                return $default;
            }
            $current = $current[$item];
        }
        return $current;
    }

    public static function issetByPath(array $array, $path)
    {
        if (is_null($path)) {
            return true;
        }
        $path = (array) $path;

        $current = $array;
        foreach($path as $item) {
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
                return (count($arrayCases) > 0) ? $arrayCases[count($arrayCases)-1] : null;
                break;
            default :
                return $default;
        }
    }

    public static function sortByKey($array, $key = 'order')
    {
        usort($array, function($a, $b) use ($key) {
            return $a[$key] - $b[$key];
        });
        return $array;
    }

    public static function getSlice($array, $path, $default = null)
    {
        $valArray = [];
        foreach($array as $key=>$value) {
            if (!is_array($value)) {
                $valArray[$key] = $default;
            } else {
                $valArray[$key] = self::getByPath($value, $path, $default);
            }
        }
        return $valArray;
    }

    public static function filterNulls(array $array)
    {
        return array_filter($array, function($var) {
            return !is_null($var);
        });
    }

    /**
     * @param array $array
     * @return bool
     * @deprecated use getArrayType with 3 value: assoc (1), num (-1), combined (0)
     */
    public static function isAssoc(array $array) {
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
        foreach($keys as $key) {
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
        foreach($array as $key=>$value) {
            $preparedArray[] = $key . $operator . $value;
        }
        return implode($glue, $preparedArray);
    }

    public static function implodeRecursive($glue = "", array $array)
    {
        foreach($array as $key=>$value) {
            if (is_array($value)) {
                $array[$key] = self::implodeRecursive($glue, $value);
            }
        }
        return implode($glue, $value);
    }

    public static function isMulti($a){
        foreach($a as $v) {
            if(is_array($v)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public static function renameKeys($array, Callable $function)
    {
        $keys = array_keys($array);
        $keys = array_map($function, $keys);
        $array = array_combine($keys, $array);
        return $array;
    }
}

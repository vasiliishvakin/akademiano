<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace OrbisTools;

class ArrayUtils {
    public static function merge_recursive()
    {
        $arrays = func_get_args();
        $merged = array_shift($arrays);
        foreach ($arrays as $currentArray) {
            foreach ($currentArray as $key => $value) {
                if (is_array($value) && isset ($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = self::merge_recursive($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
        }
        return $merged;
    }

    public static function getByPath(array $array, array $path = null, $default = null)
    {
        if (is_null($path)) {
            return $array;
        }

        $current = $array;
        foreach($path as $item) {
            if (!isset($current[$item])) {
                return $default;
            }
            $current = $item;
        }
        return $current;
    }

    public function issetByPath(array $array, array $path)
    {
        $current = $array;
        foreach($path as $item) {
            if (!isset($current[$item])) {
                return false;
            }
            $current = $item;
        }
        return true;
    }
}
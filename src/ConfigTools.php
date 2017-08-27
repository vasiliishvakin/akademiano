<?php


namespace Akademiano\Config;


class ConfigTools
{
    /**
     * @param array
     * @param array
     * @return array
     */
    public static function merge()
    {
        $arrays = func_get_args();
        $merged = array_shift($arrays);
        foreach ($arrays as $currentArray) {
            foreach ($currentArray as $key => $value) {
                if (is_array($value) && isset ($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = self::merge($merged[$key], $value);
                } else {
                    //проверяем что это не отключение параметров
                    $unSetted = false;
                    if ($value === null && !is_integer($key)) {
                        if (false !== $keyMerged = array_search($key, $merged)) {
                            if (is_integer($keyMerged)) {
                                unset($merged[$keyMerged]);
                                $unSetted = true;
                            }
                        }
                    }
                    if (!$unSetted) {
                        //Числовой ключ добавляем, если такого значения нет, иначе заменяем
                        if (is_integer($key) && is_string($value)) {
                            if (!array_search($value, $merged)) {
                                $merged[] = $value;
                            }
                        } else {
                            $merged[$key] = $value;
                        }
                    }
                }
            }
        }
        return $merged;
    }
}

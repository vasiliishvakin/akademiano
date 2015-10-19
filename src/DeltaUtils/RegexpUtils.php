<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 19.10.2015
 * Time: 16:39
 */

namespace DeltaUtils;


class RegexpUtils
{
    public static function addDelimiters($string, $delimiter = "#")
    {
        if (mb_strlen($string) >= 3) {
            $delimiters = ["#", "/", "~"];
            $a1 = mb_substr($string, 0, 1);
            if (in_array($a1, $delimiters)) {
                $b1 = mb_substr($string, -1, 1);
                if ($a1 === $b1) {
                    if ($a1 !== "/") {
                        return $string;
                    } else {
                        $a2 = substr($string, 1, 1);
                        $b2 = substr($string, -2, 1);
                        if ($a2 === "/" && $a2 === $b2) {
                            return $string;
                        }
                    }
                }
            }
        }
        return $delimiter . $string . $delimiter;
    }

    public static function simpleToNormal($string)
    {
        return preg_replace("~{:(\w+)}~", "(?P<$1>\\w+)", $string);
    }

}

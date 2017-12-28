<?php

namespace Akademiano\Utils;

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

    public static function simpleToNormal($string, $mask = "\\w+")
    {
        return preg_replace("~{:(\w+)}~", "(?P<$1>{$mask})", $string);
    }

    public static function replaceNamedParams($regexp, $params)
    {
        $regexp = ltrim($regexp, "^");
        $regexp = rtrim($regexp, "$");
        if (!empty($params)) {
            $link = preg_replace_callback('~\(\?P<(\w+)>.+\)~U', function ($match) use (&$params) {
                return ArrayTools::extract($params, $match[1], $match[0]);
            }, $regexp);
        } else {
            $link = preg_replace('~\(\?P<(\w+)>.+\)~U', "", $regexp);
        }
        $link = preg_replace(['~(\/)(\?+)~', '~\?+$~'], ["$1"], $link);
        //clear double slashes, escaped chars
        $link = preg_replace(['#/+#', '#\\\+#'], ['/', ''], $link);
        return $link;
    }
}

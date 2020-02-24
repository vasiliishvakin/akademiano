<?php


namespace Akademiano\Utils;


class Debug
{
    public static function var2str($var)
    {
        return json_encode($var);
    }

    public static function var_export($expression, $return = FALSE): ?string
    {
        $export = var_export($expression, TRUE);
        $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
        $array = preg_split("/\r\n|\n|\r/", $export);
        $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [NULL, ']$1', ' => ['], $array);
        $export = implode(PHP_EOL, array_filter(["["] + $array));
        if ((bool)$return) {
            return $export;
        }
        echo $export;
        return null;
    }

}

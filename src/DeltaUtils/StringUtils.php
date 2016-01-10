<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaUtils;


use DeltaUtils\Helper\Pluralizer;

class StringUtils
{
    public static function cropBySpace($str, $size)
    {
        return mb_substr($str, 0, mb_strrpos(mb_substr($str, 0, $size), ' '));
    }

    public static function nl2p($string)
    {
        $paragraphs = '';

        foreach (explode("\n", $string) as $line) {
            if (trim($line)) {
                $paragraphs .= '<p>' . $line . '</p>';
            }
        }

        return $paragraphs;
    }

    public static function urlToTag($text, $maxLen = 0)
    {
        $pattern = '((http|ftp)(s)?+(://)?(([-\w]+\.)+([^\s]+)+[^,.\s]))';
        $newText = preg_replace_callback($pattern,
            function ($matches) use ($maxLen) {
                $url = $matches[0];
                $urlArr = parse_url($url);
                $path = (isset($urlArr["path"])) ? $urlArr["path"] : "";
                $title = $urlArr["host"] . $path;
                if ($maxLen === 0) {
                    return "<a href='{$url}'>{$title}</a>";
                }
                $maxTitle = $title;
                if ($maxLen < mb_strlen($urlArr["host"])) {
                    $maxLen = mb_strlen($urlArr["host"]) < 50 ? mb_strlen($urlArr["host"]) : 50;
                }

                if (mb_strlen($title) > $maxLen) {
                    $title = mb_substr($title, 0, $maxLen) . "...";
                }

                return "<a title='$maxTitle' href='{$url}'>{$title}</a>";
            },
            $text);

        return (!is_null($newText)) ? $newText : $text;
    }

    public static function cutStr($text, $length = 160)
    {
        $text = strip_tags($text);
        $buf = 10;
        if (mb_strlen($text) <= $length + $buf) {
            return $text;
        }
        $preStr = mb_substr($text, 0, $length + $buf);
        $chars = [".", "!", ",", "\n", " ",];
        $startPos = $length - $buf;
        foreach ($chars as $char) {
            $pos = mb_strpos($preStr, $char, $startPos);
            if ($pos !== false) {
                break;
            }
        }
        if (!$pos) {
            $pos = $length + $buf;
        }

        return trim(mb_substr($text, 0, $pos + 1));
    }

    public static function cutClassName($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $class = explode("\\", $class);
        $class = end($class);

        return $class;
    }

    public static function cutNamespace($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $namespace = substr($class, 0, strrpos($class, '\\'));

        return $namespace;
    }

    public static function nl2Array($string)
    {
        return explode("\n", $string);
    }

    public static function lowDashToCamelCase($string)
    {
        if (strpos($string, "_")) {
            $stringParts = explode("_", $string);
            foreach ($stringParts as $key => $part) {
                if ($key === 0) {
                    continue;
                }
                $stringParts[$key] = ucfirst($part);
            }
            $string = implode("", $stringParts);
        }

        return $string;
    }

    public static function camelCaseToLowDash($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }

    public static function toIdStr9($id)
    {
        return sprintf("id%09s", (integer)$id);
    }

    public static function idFromStr($string)
    {
        return (integer)substr($string, 2);
    }

    public static function toString($var, $oneField = false, $implode = false)
    {
        if ($oneField === true) {
            $oneField = "id";
        }

        if ($implode === true) {
            $implode = ", ";
        }

        switch (gettype($var)) {
            case "boolean":
            case "integer":
            case "double":
            case "string":
            case "NULL":
                return (string)$var;
            case "array" :
                if (!$oneField) {
                    return $implode ? ArrayUtils::implodeRecursive($implode, $var) : "";
                } else {
                    return self::toString(array_key_exists($oneField, $var) ? $var[$oneField] : "", $oneField,
                        $implode);
                }
            case "object" :
                if ($var instanceof \DateTime) {
                    return $var->format("Y-m-d h:i:s");
                }
                if ($oneField) {
                    if (property_exists($var, $oneField)) {
                        return self::toString($var->{$oneField}, $oneField, $implode);
                    }
                    if (method_exists($var, $oneField)) {
                        return self::toString($var->{$oneField}(), $oneField, $implode);
                    }
                    $method = "get" . ucfirst($oneField);
                    if (method_exists($var, $method)) {
                        return self::toString($var->{$method}(), $oneField, $implode);
                    }

                    return "";
                } else {
                    if (method_exists($var, "__toString")) {
                        return (string)$var;
                    }
                    if (method_exists($var, "toArray")) {
                        return self::toString($var->toArray());
                    }

                    return self::toString(get_object_vars($var), $oneField, $implode);
                }
            default:
                throw new \InvalidArgumentException("work with type " . gettype($var) . " not implemented");
        }

    }

    /**
     * Get the plural form of the given english word.
     *
     * @param  string $value
     * @param  int $count
     * @return string
     */
    public static function pluralEn($value, $count = 2)
    {
        return Pluralizer::plural($value, $count);
    }

    /**
     * Get the singular form of the given english word.
     *
     * @param  string $value
     * @return string
     */
    public static function singularEn($value)
    {
        return Pluralizer::singular($value);
    }

    public static function isText($value)
    {
        return is_string($value) && $value!=='' && !is_numeric($value);
    }

    public static function isFullClass($value)
    {
        return false !== strpos($value, "\\");
    }
}

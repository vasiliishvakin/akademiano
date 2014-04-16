<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaUtils;


class StringUtils 
{
    public static function cropBySpace($str, $size){
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

    public static function urlToTag($text, $maxLen = 0) {
        $pattern = '((http|ftp)(s)?+(://)?(([-\w]+\.)+([^\s]+)+[^,.\s]))';
        $newText = preg_replace_callback($pattern,
            function ($matches) use($maxLen) {
                $url = $matches[0];
                $urlArr = parse_url($url);
                $path = (isset($urlArr["path"])) ?  $urlArr["path"] : "";
                $title =  $urlArr["host"] . $path;
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
} 
<?php
/**
 * User: orbisnull
 * Date: 07.09.13
 */

namespace OrbisTools;

use OrbisTools\Time;

class Header 
{
    public static function mime($file)
    {
        $finfo = new \finfo(FILEINFO_MIME);
        $mime = $finfo->file($file);
        header("Content-Type: $mime");
    }

    public static function accel($uri, $file)
    {
        if (headers_sent()) {
            throw new \LogicException('Headers already send');
        }
        self::mimeHeader($file);
        header("X-Accel-Redirect: $uri");
    }

    public static  function noCache()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Pragma: no-cache"); // HTTP/1.0
    }

    public static function cache($time = 10800)
    {
        if (headers_sent()) {
            throw new \LogicException('Headers already send');
        }
        $time = Time::toSeconds($time);
        $ts = gmdate("D, d M Y H:i:s", time() + $time) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$time");
    }

    public static function modified($time = null)
    {
        if (headers_sent()) {
            throw new \LogicException('Headers already send');
        }
        $time = (!is_null($time)) ? $time : time();
        $ts = gmdate("D, d M Y H:i:s", $time) . " GMT";
        header("Last-Modified: " . $ts);
    }
}
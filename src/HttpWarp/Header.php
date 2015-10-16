<?php
/**
 * User: orbisnull
 * Date: 07.09.13
 */

namespace HttpWarp;

use DeltaUtils\Time;

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
        self::mimeHeader($file);
        header("X-Accel-Redirect: $uri");
    }

    public static  function noCache()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Pragma: no-cache"); // HTTP/1.0
    }

    public static function cache($time = 10800)
    {
        $time = Time::toSeconds($time);
        header("Expires: " . self::toGmtDate(time() + $time));
        header("Pragma: cache");
        header("Cache-Control: max-age=$time");
        header("X-Accel-Expires : " . $time);
    }

    public static function toGmtDate($time = null)
    {
        if(null === $time) {
            $time = time();
        }
        return gmdate("D, d M Y H:i:s", $time) . " GMT";
    }

    public static function modified($time = null)
    {
        $time = (!is_null($time)) ? $time : time();
        $ts = self::toGmtDate($time);
        header("Last-Modified: " . $ts);
    }
}

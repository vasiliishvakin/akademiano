<?php
/**
 * User: orbisnull
 * Date: 07.09.13
 */

namespace OrbisTools;


class Time
{
    public static function toSeconds($timeStr)
    {
        $timeStr = trim($timeStr);
        $unit = substr($timeStr, -1, 1);
        if (is_int($unit)) {
            return $timeStr;
        }
        $time =(int)substr($timeStr, 0, -1);
        switch ($unit) {
            case 'd' :
                $time = $time * 24;
            case 'h' :
                $time = $time * 60;
            case 'm' :
                $time = $time * 60;
        }
        return $time;
    }

}
<?php
/**
 * User: orbisnull
 * Date: 07.09.13
 */

namespace DeltaUtils;


class Time
{
    public static function toSeconds($timeStr)
    {
        if (is_int($timeStr)) {
            return $timeStr;
        }
        $timeStr = trim($timeStr);
        $unit = substr($timeStr, -1, 1);
        $time = (int)substr($timeStr, 0, -1);
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

    public static function intervalSeconds(\DateInterval $dateInterval)
    {
        return ($dateInterval->y * 365 * 24 * 60 * 60) +
        ($dateInterval->m * 30 * 24 * 60 * 60) +
        ($dateInterval->d * 24 * 60 * 60) +
        ($dateInterval->h * 60 * 60) +
        ($dateInterval->i * 60) +
        $dateInterval->s;
    }

}
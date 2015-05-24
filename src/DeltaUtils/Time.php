<?php
/**
 * User: orbisnull
 * Date: 07.09.13
 */

namespace DeltaUtils;

class Time
{
    /**
     * @param $timeStr
     * @return int
     */
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

    public static function toStrIntl($date, $format, $locale = null)
    {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }
        if ($locale) {
            $currentLocale = System::setLocale("LC_TIME", $locale);
        }
        $timeStamp = $date->getTimestamp();
        $string =  strftime($format, $timeStamp);
        if ($locale) {
            System::setLocale("LC_TIME", $currentLocale);
        }
        return $string;
    }

    public static function str2DateTime($date)
    {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }
        return $date;
    }

    public static function calendarMonth($date = null,  array $linkDates = null, $activeDay = null)
    {
        $date = self::str2DateTime($date);
        $activeDay = $activeDay ? self::str2DateTime($activeDay) : null;
        $now = new \DateTime();

        $countDays = $date->format('t');
        $firstDayObj = clone $date;
        $firstDayObj->modify("first day of this month");
        $lastDayObj = clone $date;
        $lastDayObj->modify("last day of this month");
        $firstDay = $firstDayObj->format("w");
        $j = 1;
        $month = [];
        $week = [];
        while ($j < $firstDay) {
            $week[] = null;
            $j++;
        }
        $os = $j + 1;
        //set days
        for ($i = 1; $i <= $countDays; $i++) {
            $day = [
                "number" => $i,
                "date" => new \DateTime($date->format("Y-m-") . $i),
            ];
            $dayStr = $day["date"]->format("Ymd");
            $day["now"] = $dayStr == $now->format("Ymd");
            $day["active"] = $activeDay ? $dayStr == $activeDay->format("Ymd") : false;
            if ($linkDates) {
                if (isset($linkDates[$dayStr])) {
                    $day["uri"] = is_array($linkDates[$dayStr]) ? $linkDates[$dayStr]["uri"] : $linkDates[$dayStr];
                }
            }

            $week[] = $day;
            if (round($j / 7) - $j / 7 == 0){
                $month[] = $week;
                $week = [];
            }
            $j++;
        }
        while ($os <= 7) {
            $week[] = null;;
            $os++;
        }
        $data = [
            "initDate" => $date,
            "month" => $date->format("m"),
            "year" => $date->format("Y"),
            "days" => $month,
            "firstDay" => $firstDayObj,
            "lastDay" => $lastDayObj,
        ];
        return $data;
    }

}
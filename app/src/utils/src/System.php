<?php

namespace Akademiano\Utils;


class System
{
    public static function setLocale($category, $locale)
    {
        $currentLocale = setlocale($category, null);
        if (is_array($locale) || $currentLocale !== $locale) {
            $locale = (array)$locale;
            $newLocale = false;
            foreach ($locale as $oneLocale) {
                $newLocale = setlocale($category, $oneLocale);
                if ($newLocale) {
                    break;
                }
            }
            if (!$newLocale) {
                throw new \Exception("Locale " . implode("|", (array)$locale) . " not applied");
            }
        }

        return $currentLocale;
    }
}

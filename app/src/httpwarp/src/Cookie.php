<?php

namespace Akademiano\HttpWarp;


class Cookie
{
    public static function setCookie($name, $value, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = true)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    public static function getCookie($name)
    {
        return isset($_COOKIE) && isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    public static function hasCookie($name)
    {
        return isset($_COOKIE) && isset($_COOKIE[$name]);
    }

}


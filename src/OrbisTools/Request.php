<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace OrbisTools;


class Request
{
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    public function hasVar($name)
    {
        $method = $this->getMethod();
        switch ($method) {
            case 'POST' :
                return isset($_POST[$name]);
                break;
            case 'GET' :
                return isset($_GET[$name]);
                break;
        }
    }

    public function getVar($name, $default = null)
    {
        $method = $this->getMethod();
        switch ($method) {
            case 'POST' :
                return (isset($_POST[$name])) ? $_POST[$name] : $default;
                break;
            case 'GET' :
                return (isset($_GET[$name])) ? $_GET[$name] : $default;
                break;
        }
    }

} 
<?php

namespace Kindrouter;

class Request
{
    protected $params;

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getParams()
    {
        if (is_null($this->params)) {
            switch ($this->getMethod()) {
                case 'GET':
                    $this->params = $_GET;
                    break;
                case 'POST':
                    $this->params = $_POST;
                    break;
                case 'PUT':
                    parse_str(file_get_contents('php://input'), $this->params);
                    break;
                default:
                    throw new Exception('Method ' . $this->getMethod() . 'not supported');
            }
        }
        return $this->params;
    }

    public function getParam($key, $default = null)
    {
        if (!isset($this->getParams()[$key])) {
            return $default;
        }
        return $this->getParams()[$key];
    }

    public function getDecodeJson($key, $default = null)
    {
        ErrorHandler::start();
        $params = json_decode($this->getParam($key), $default);
        ErrorHandler::stop();
        return (array) $params;
    }
}
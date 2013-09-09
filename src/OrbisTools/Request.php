<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace OrbisTools;


class Request
{
    protected $method;
    protected $params;
    protected $uri;
    protected $uriNormal;

    public function getMethod()
    {
        if (is_null($this->method)) {
            $this->method = $_SERVER['REQUEST_METHOD'];
        }
        return $this->method;
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

    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    public function hasParam($name)
    {
        return isset($this->getParams()[$name]);
    }

    /**
     * @deprecated
     */
    public function hasVar($name)
    {
        return $this->hasParam($name);
    }

    /**
     * @deprecated
     */
    public function getVar($name, $default = null)
    {
        return $this->getParam($name, $default);
    }

    public function getDecodeJson($key, $default = null)
    {
        ErrorHandler::start();
        $params = json_decode($this->getParam($key), $default);
        ErrorHandler::stop();
        return (array) $params;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        if (is_null($this->uri)) {
            $this->uri = $_SERVER['REQUEST_URI'];
        }
        return $this->uri;
    }

    public function getUriNormal()
    {
        if (is_null($this->uriNormal)) {
            $uri = $this->getUri();
            if (($pos = strpos($uri, '?')) !== false) {
                $uri = substr($uri, 0, $pos);
            }
            $uri = preg_replace('~(\/){2,}~', '/', $uri);
            if (empty($uri)) {
                $uri = '/';
            }
            $this->uriNormal = $uri;
        }
        return $this->uriNormal;
    }



} 
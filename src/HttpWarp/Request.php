<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace HttpWarp;

use DeltaUtils\ArrayUtils;
use DeltaUtils\ErrorHandler;
use HttpWarp\File\UploadFile;

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

    public function getParams($emptyStringNull = true)
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
                    throw new \Exception('Method ' . $this->getMethod() . 'not supported');
            }
            if ($emptyStringNull) {
                foreach($this->params as $key=>$value) {
                    if ($value==="") {
                        $this->params[$key] = null;
                    }
                }
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
        return (array)$params;
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

    public function getUriPartByNum($num = 0, $default = null)
    {
        $uri = trim($this->getUriNormal(), '/');
        if ($num === 0) {
            return $uri;
        }
        $uri = explode('/', $uri);
        $num = ($num < 0) ? $num = count($uri) + $num : $num = $num - 1;
        if ($num > count($uri) - 1) {
            return $default;
        }
        return $uri[$num];
    }

    public function getUriPartsCount()
    {
        $uri = trim($this->getUriNormal(), '/');
        $uri = explode('/', $uri);
        return count($uri);
    }


    /**
     * @param string $name
     * @param null $type
     * @param null $maxSize
     * @param bool $withErrors
     * @return UploadFile[]
     */
    public function getFiles($name = "files", $type = null, $maxSize = null, $withErrors = false)
    {
        $files = [];
        if (!isset($_FILES) || empty($_FILES) || !isset($_FILES[$name])) {
            return $files;
        }
        $inFiles = $_FILES[$name];
        $countFiles = count($inFiles["name"]);
        if ($countFiles == 1) {
            $inFiles = [$inFiles];
        } elseif ($countFiles > 1) {
            $newInFiles = [];
            foreach ($inFiles as $key => $info) {
                for ($i = 0; $i < $countFiles; $i++) {
                    $newInFiles[$i][$key] = $info[$i];
                }
            }
            $inFiles = $newInFiles;
        }

        foreach ($inFiles as $fileData) {
            foreach ($fileData as $key => $value) {
                if (is_array($value)) {
                    if (count($value) > 1) {
                        throw new \LogicException("To many values in file param");
                    }
                    $fileData[$key] = reset($value);
                }
            }
            if (!$withErrors && $fileData["error"] !== 0) {
                continue;
            }
            $file = new UploadFile($fileData["name"], $fileData["tmp_name"], $fileData["error"]);
            if ($type && !$file->checkType($type)) {
                continue;
            }
            if ($maxSize && $file->getSize() > $maxSize) {
                continue;
            }
            $files[] = $file;
        }
        return $files;
    }

    public function isHttps()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==="on");
    }

    public function getProtocol()
    {
        return $this->isHttps() ? "https" : "http";
    }
}

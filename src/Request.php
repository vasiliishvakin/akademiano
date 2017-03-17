<?php

namespace Akademiano\HttpWarp;

use Akademiano\Utils\CatchError;
use Akademiano\HttpWarp\File\UploadFile;

class Request
{
    protected $method;
    protected $params;
    protected $url;
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
                case 'HEAD' :
                    $this->params = $_GET;
                    break;
                case 'POST':
                    $this->params = $_POST;
                    break;
                case 'PUT':
                case 'DELETE':
                    parse_str(file_get_contents('php://input'), $this->params);
                    break;
                default:
                    throw new \Exception('Method ' . $this->getMethod() . 'not supported');
            }
            if ($emptyStringNull) {
                foreach ($this->params as $key => $value) {
                    if ($value === "") {
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

    public function getDecodeJson($key, $default = null)
    {
        CatchError::start();
        $params = json_decode($this->getParam($key), $default);
        CatchError::stop();
        return (array)$params;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        if (!$url instanceof Url) {
            $url = new Url($url);
        }
        $this->url = $url;
    }

    /**
     * @return Url
     */
    public function getUrl()
    {
        if (is_null($this->url)) {
            $url = new Url();
            $url->setScheme($this->isHttps() ? "https" : "http");
            $url->setDomain($_SERVER["HTTP_HOST"] ?: $_SERVER["SERVER_NAME"]);
            $url->setPort($_SERVER["SERVER_PORT"]);
            $url->setPath($_SERVER["REQUEST_URI"]);
            if (isset($_SERVER["QUERY_STRING"])) {
                $url->setQuery($_SERVER["QUERY_STRING"]);
            }
            $this->url = $url;
        }
        return $this->url;
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

    public function isCheckModified()
    {
        return isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : null;
    }
}

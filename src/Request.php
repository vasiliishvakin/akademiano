<?php

namespace Akademiano\HttpWarp;

use Akademiano\HttpWarp\Parts\EnvironmentIncludeTrait;
use Akademiano\Utils\CatchError;
use Akademiano\HttpWarp\File\UploadFile;

class Request implements EnvironmentIncludeInterface
{
    use EnvironmentIncludeTrait;

    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_HEAD = "HEAD";
    const METHOD_DELETE = "DELETE";

    protected $method;
    protected $params;
    protected $url;
    protected $uriNormal;

    /** @var  array */
    protected $rawFiles;

    /** @var  File\UploadFile[] */
    protected $files = [];

    /**
     * Request constructor.
     * @param Environment $environment
     */
    public function __construct(Environment $environment = null)
    {
        if (null !== $environment) {
            $this->setEnvironment($environment);
        }
    }

    public function getMethod()
    {
        if (is_null($this->method)) {
            $this->method = $this->getEnvironment()->getRequestMethod();
        }
        return $this->method;
    }

    public function getParams($emptyStringNull = true)
    {
        if (is_null($this->params)) {
            switch ($this->getMethod()) {
                case self::METHOD_GET:
                case self::METHOD_HEAD :
                    $this->params = $_GET;
                case self::METHOD_POST:
                    $this->params = array_merge($_GET, $_POST);
                    break;
                default:
                    $stParams = [];
                    parse_str(file_get_contents('php://input'), $stParams);
                    $this->params = array_merge($_GET, $stParams);
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

    public function setParam(string $name, $value)
    {
        if (is_null($this->params)) {
            $this->getParams();
        }
        $this->params[$name] = $value;
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
        return $this->getMethod() === self::METHOD_GET;
    }

    public function isPost()
    {
        return $this->getMethod() === self::METHOD_POST;
    }

    public function hasParam($name)
    {
        return array_key_exists($name, $this->getParams());
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
            $url = new Url(null, $this->getEnvironment());
            $url->setPath($this->getEnvironment()->getRequestUri());
            if (null !== $this->getEnvironment()->getQueryString()) {
                $url->setQuery($this->getEnvironment()->getQueryString());
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
        if (!isset($this->files[$name])) {
            $this->files[$name] = [];
            if (isset($_FILES) && !empty($_FILES) && isset($_FILES[$name])) {
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
                    $this->files[$name][] = $file;
                }
            }
        }
        return $this->files[$name];
    }

    public function isCheckModified()
    {
        return isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : null;
    }
}

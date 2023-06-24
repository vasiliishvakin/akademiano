<?php

namespace Akademiano\HttpWarp;


use PhpOption\None;
use PhpOption\Some;

class Environment
{
    protected $serverName;
    protected $port;
    /** @var  boolean */
    protected $https;

    protected $accept;

    protected $isEqualServerEnv;

    protected $requestMethod;

    protected $requestUri;

    protected $queryString;

    public function getSrvScheme()
    {
        return $this->isSrvHttps() ? "https" : "http";
    }

    /**
     * @return mixed
     */
    public function getScheme()
    {
        return $this->isHttps() ? "https" : "http";
    }

    public function isSrvHttps()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === "on");
    }

    /**
     * @return boolean
     */
    public function isHttps()
    {
        if (null === $this->https) {
            $this->https = $this->isSrvHttps();
        }
        return $this->https;
    }

    /**
     * @param boolean $https
     */
    public function setHttps($https)
    {
        $this->https = $https;
    }

    public function getSrvServerName()
    {
        return isset($_SERVER["HTTP_HOST"]) ? strtok($_SERVER['HTTP_HOST'], ':') :
            (isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : null);
    }

    /**
     * @return mixed
     */
    public function getServerName()
    {
        if (null === $this->serverName) {
            $this->serverName = $this->getSrvServerName();
        }
        return $this->serverName;
    }

    /**
     * @param mixed $serverName
     */
    public function setServerName($serverName)
    {
        $this->serverName = $serverName;
    }

    public function getSrvPort()
    {
        return $_SERVER["SERVER_PORT"];
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        if (null === $this->port) {
            $this->port = $this->getSrvPort();
        }
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     * @deprecated
     */
    public function isSrvEnv()
    {
        if (null === $this->isEqualServerEnv) {
            $this->isEqualServerEnv = ($this->getScheme() === $this->getSrvScheme()) && ($this->getServerName() === $this->getSrvServerName()) && ($this->getPort() === $this->getSrvPort());
        }
        return $this->isEqualServerEnv;
    }

    /**
     * @return mixed
     */
    public function getRequestMethod()
    {
        if (null === $this->requestMethod) {
            if (isset($_SERVER["REQUEST_METHOD"])) {
                $this->requestMethod = $_SERVER["REQUEST_METHOD"];
            }
        }
        return $this->requestMethod;
    }

    /**
     * @param mixed $requestMethod
     */
    public function setRequestMethod($requestMethod)
    {
        $this->requestMethod = $requestMethod;
    }

    /**
     * @return mixed
     */
    public function getRequestUri()
    {
        if (null === $this->requestUri) {
            if (isset($_SERVER["REQUEST_URI"])) {
                $this->requestUri = $_SERVER["REQUEST_URI"];
            }
        }
        return $this->requestUri;
    }

    /**
     * @param mixed $requestUri
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;
    }

    /**
     * @return mixed
     */
    public function getQueryString()
    {
        if (null === $this->queryString) {
            if (isset($_SERVER["QUERY_STRING"])) {
                $this->queryString = $_SERVER["QUERY_STRING"];
            }
        }
        return $this->queryString;
    }

    /**
     * @param mixed $queryString
     */
    public function setQueryString($queryString)
    {
        $this->queryString = $queryString;
    }

    public function getVar($varName, $local_only = false, $default = null)
    {
        $value = getenv($varName, $local_only);
        return (false !== $value) ? $value : $default;
    }

    public function getVarMayby($varName, $local_only = false)
    {
        $value = $this->getVar($varName, $local_only);
        return (null !== $value) ? new Some($value) : None::create();
    }

    public function getSapiName()
    {
        return php_sapi_name();
    }

    public function isCli()
    {
        return $this->getSapiName() === "cli";
    }

    public function setAccept(string $accept)
    {
        $this->accept = $accept;
    }

    public function getAccept(): array
    {
        if (null === $this->accept) {
            if (isset($_SERVER["HTTP_ACCEPT"])) {
                $this->accept = $_SERVER["HTTP_ACCEPT"];
            }
        }
        if (is_string($this->accept)) {
            $accept = explode(',', $this->accept);
            $AcceptTypes = [];
            foreach ($accept as $a) {
                // the default quality is 1.
                $q = 1;
                // check if there is a different quality
                if (strpos($a, ';q=')) {
                    // divide "mime/type;q=X" into two parts: "mime/type" i "X"
                    list($a, $q) = explode(';q=', $a);
                }
                // mime-type $a is accepted with the quality $q
                // WARNING: $q == 0 means, that mime-type isn’t supported!
                $AcceptTypes[$a] = $q;
            }
            arsort($AcceptTypes);
            $this->accept=array_keys($AcceptTypes);
        }
        return $this->accept;
    }
}

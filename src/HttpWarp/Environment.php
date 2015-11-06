<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 27.10.2015
 * Time: 23:12
 */

namespace HttpWarp;


class Environment
{
    protected $serverName;
    protected $port;
    /** @var  boolean */
    protected $https;


    /**
     * @return mixed
     */
    public function getScheme()
    {
        return $this->isHttps() ? "https" : "http";
    }

    /**
     * @return boolean
     */
    public function isHttps()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==="on");
    }

    /**
     * @param boolean $https
     */
    public function setHttps($https)
    {
        $this->https = $https;
    }

    /**
     * @return mixed
     */
    public function getServerName()
    {
        if (null === $this->serverName) {
            $this->serverName = $_SERVER["HTTP_HOST"] ?: $_SERVER["SERVER_NAME"];
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

    /**
     * @return mixed
     */
    public function getPort()
    {
        if (null === $this->port) {
            $this->port = $_SERVER["SERVER_PORT"];
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


}
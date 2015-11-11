<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 08.10.2015
 * Time: 12:40
 */

namespace HttpWarp;

use HttpWarp\Url\Path;
use HttpWarp\Url\Query;

/**
 * Class Url
 * @package HttpWarp
 */
class Url
{
    protected $defaultPorts = [80, 443];
    protected $rawUrl;
    protected $scheme;
    protected $domain;
    protected $port;
    protected $user;
    protected $password;
    protected $rawPath;
    protected $path;
    protected $query;
    protected $fragment;

    function __construct($url = null)
    {
        if (!is_null($url)) {
            $this->setUrl($url);
        }
    }

    public function getId()
    {
        return $this->getUrl();
    }

    /**
     * @return mixed
     */
    public function getScheme()
    {
        if (is_null($this->scheme)) {
            $this->scheme = isset($this->port) && ($this->port == 443) ? "https" : "http";
        }

        return $this->scheme;
    }

    /**
     * @param mixed $scheme
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        if (is_null($this->domain)) {
            $this->domain = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : $_SERVER["SERVER_NAME"];
        }

        return $this->domain;
    }

    /**
     * @param mixed $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        if (is_null($this->port)) {
            $this->port = isset($this->scheme) && $this->scheme === "https" ? 443 : 80;
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
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return Path
     */
    public function getPath()
    {
        if (!$this->path instanceof Path) {
            $this->path = new Path($this->path);
        }

        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        if (!$this->query instanceof Query) {
            $this->query = new Query($this->query);
        }

        return $this->query;
    }

    /**
     * @param string|Query $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param mixed $fragment
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->rawUrl = $url;
        $this->load($this->parse($url));
    }

    /**
     * @return mixed
     */
    public function getRawUrl()
    {
        return $this->rawUrl;
    }

    public function load(array $components)
    {
        foreach ($components as $name => $value) {
            $method = "set" . ucfirst($name);
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }

        return $this;
    }

    public function parse($url)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException("url empty");
        }

        return parse_url($url);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $port = in_array($this->getPort(), $this->defaultPorts) ? "" : ":" . $this->getPort();
        $path = (string)$this->getPath();
        $query = (string)$this->getQuery();
        $query = !empty($query) ? "?" . $query : "";
        $fragment = $this->getFragment();
        $fragment = !empty($fragment) ? "#" . $fragment : "";
        if ($path === "/" && (empty($query) && empty($fragment))) {
            $path = "";
        }
        $url = $this->getScheme() . "://" . $this->getDomain() . $port . $path . $query . $fragment;

        return $url;
    }

    public function __toString()
    {
        return $this->getUrl();
    }
}

<?php

namespace Akademiano\Core\Exception;


use Akademiano\HttpWarp\Exception\HttpUsableException;

class AccessDeniedException extends \Akademiano\HttpWarp\Exception\AccessDeniedException
{
    protected $resource;
    protected $url;

    public function __construct($message = "", $code = 0, \Exception $previous = null, $resource = null, $url = null)
    {
        $code = 403;
        parent::__construct($message, $code, $previous);
        $this->resource = !empty($resource) ? (string) $resource : null;
        $this->url = !empty($url) ? (string) $url : null;
    }

    /**
     * @return null|string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param null|string $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return null|string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param null|string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}

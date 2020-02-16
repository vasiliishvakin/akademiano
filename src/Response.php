<?php

namespace Akademiano\HttpWarp;

use Akademiano\Utils\Time;

class Response
{
    protected $body;
    protected $code = 200;
    protected $contentType = 'text/html';
    protected $charset = "utf-8";
    protected $language = "en";
    protected $modified;
    protected $timeToCache;
    protected $etag;
    protected $redirectUrl;
    protected $goRedirect = true;

    /**
     * @param array $config
     * @deprecated
     */
    public function setConfig($config)
    {
        $this->setDefaults($config);
    }

    public function setDefaults(array $params)
    {
        foreach ($params as $name => $value) {
            $method = "set" . ucfirst($name);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    public function setContentJson()
    {
        $this->setContentType('application/json');
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param $modified
     * @param bool $onlyBigger
     */
    public function setModified($modified, $onlyBigger = false)
    {
        if ($modified instanceof \DateTime) {
            $modified = $modified->getTimestamp();
        }
        $modified = (int)$modified;
        $this->modified = !$onlyBigger ? $modified : (($modified > $this->modified) ? $modified : $this->modified);
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        if (is_null($this->modified)) {
            $this->modified = time();
        }
        return $this->modified;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param int|string $timeToCache
     */
    public function setTimeToCache($timeToCache)
    {
        $this->timeToCache = Time::toSeconds($timeToCache);
    }

    /**
     * @return int
     */
    public function getTimeToCache()
    {
        return $this->timeToCache;
    }

    public function getHttpCode()
    {
        $code = $this->getCode();
        switch ($code) {
            case 404:
                return "404  Not Found";
                break;
            case 403:
                return "403 Forbidden";
                break;
            default :
                return $code;
        }
    }

    /**
     * @param mixed $etag
     */
    public function setEtag($etag)
    {
        $this->etag = $etag;
    }

    /**
     * @return mixed
     */
    public function getEtag()
    {
        if (is_null($this->etag)) {
            $this->etag = 'W/"' . hash('md5', $this->body) . '"';
        }
        return $this->etag;
    }

    public function getLength()
    {
        return mb_strlen($this->getBody());
    }

    public function getLengthByte()
    {
        return mb_strlen($this->getBody(), '8bit');
    }

    public function sendHeaders()
    {
        if (headers_sent()) {
            throw new \LogicException('Headers already send');
        }
        header("HTTP/1.1 {$this->getHttpCode()}");
        header("Content-Type:{$this->getContentType()}; charset={$this->getCharset()}");
        header("Content-Language: {$this->getLanguage()}");
        header("X-Response-Date: " . Header::toGmtDate());

        $modified = $this->getModified();
        Header::modified($modified);

        $cacheTime = $this->getTimeToCache();
        if ($cacheTime > 0) {
            Header::cache($cacheTime);
        } elseif ($cacheTime < 0) {
            Header::noCache();
        }
        header("ETag: {$this->getEtag()}");
    }

    public function sendReplay($goRedirect = null)
    {
        if (null === $goRedirect) {
            $goRedirect = $this->isGoRedirect();
        }

        if ($goRedirect && null !== $this->getRedirectUrl()) {
            $this->redirect();
            return;
        }

        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            $etagRequest = $_SERVER['HTTP_IF_NONE_MATCH'];
            $etag = $this->getEtag();
            if ($etagRequest === $etag) {
                header('HTTP/1.0 304 Not Modified');
                header('Status: 304 Not Modified');
                return;
            }
        }

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $modifiedRequest = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
            $modifiedRequest = strtotime($modifiedRequest);
            $modified = $this->getModified();
            if ($modifiedRequest >= $modified) {
                header('HTTP/1.0 304 Not Modified');
                header('Status: 304 Not Modified');
                return;
            }
        }
        $this->sendHeaders();
        echo $this->getBody();
    }

    public function setRedirectUrl($url)
    {
        $this->redirectUrl = $url;
    }

    /**
     * @return mixed
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @return bool
     */
    public function isGoRedirect(): bool
    {
        return $this->goRedirect;
    }

    /**
     * @param bool $goRedirect
     */
    public function setGoRedirect(bool $goRedirect)
    {
        $this->goRedirect = $goRedirect;
    }

    public function redirect()
    {
        if (empty($this->redirectUrl)) {
            throw new \LogicException('Try to redirect empty url');
        }
        $url = (string)$this->getRedirectUrl();
        header(sprintf('Location: %s', $url));
    }

    public function error404()
    {
        if (headers_sent()) {
            throw new \LogicException('Headers already send, url no found');
        }
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        header("Status: 404 Not Found");
        $_SERVER['REDIRECT_STATUS'] = 404;

        echo "<h1>Not Found</h1>";
    }
}

<?php
namespace  Router;

/**
 * класс осуществляет роутинг и вызывает нужные обработчики
 */
class Router
{
    const METHOD_ALL = 'ALL';
    const SC_STR = 'str';
    const SC_REG = 'reg';
    const SC_FULL = 'full';
    const DELIMITER = '::';

    protected $urls=[];
    protected $isRun = false;

    public function setUrl($pattern, $callback, $method = self::METHOD_ALL)
    {
        if (!is_callable($callback)) {
            throw new Exception('Bad callback function');
        }

        if (($pos=(strpos($pattern, self::DELIMITER)))!==false) {
            $strSc = substr($pattern, 0 ,$pos);
            switch ($strSc) {
                case self::SC_FULL:
                    $scheme = self::SC_FULL;
                    break;
                case self::SC_REG:
                    $scheme = self::SC_REG;
                    break;
                case self::SC_STR:
                    $scheme = self::SC_STR;
                    break;
            }
            $pattern = substr($pattern, $pos + strlen(self::DELIMITER));
        }
        if (!isset($scheme)) {
            $scheme = self::SC_STR;
        }

        $this->urls[$method][$pattern] = ['pattern'=>$pattern, 'scheme'=>$scheme, 'callback' => $callback];
    }

    public function testUrl($url, $pattern, $scheme)
    {
        switch ($scheme) {
            case self::SC_FULL:
                return $url === $pattern;
                break;
            case self::SC_STR:
                return (strpos($pattern, $url) !== false);
                break;
            case self::SC_REG:
                return (bool) preg_match($pattern, $url);
                break;
            default:
                return false;
        }
    }

    public static function getUrlNormal()
    {
        static $url;
        if (is_null($url)) {
            $url = $_SERVER['REQUEST_URI'];

            if (($pos = strpos($url, '?')) !== false) {
                $url = substr($url, 0, $pos);
            }
            $url = preg_replace('~(\/){2,}~', '/', $url);
            if (empty($url)) {
                $url = '/';
            }
        }
        return $url;
    }

    public function run()
    {
        if ($this->isRun) { return; }
        $this->isRun = true;

        $methods  = [self::METHOD_ALL, $_SERVER['REQUEST_METHOD']];
        $strUrl = $this->getUrlNormal();

        foreach ($methods as &$method) {
            $currUrls = empty($this->urls[$method]) ? [] : $this->urls[$method];
            foreach ($currUrls as &$urlData) {
                if ($this->testUrl($strUrl, $urlData['pattern'], $urlData['scheme'])) {
                    return call_user_func($urlData['callback']);
                }
            }
        }
    }

    function __invoke()
    {
        return $this->run();
    }


}
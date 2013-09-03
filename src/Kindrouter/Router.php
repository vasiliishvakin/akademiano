<?php
namespace  Kindrouter;

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

    public function setUrl($pattern, $callback, $method = self::METHOD_ALL, $args = null)
    {
        if (!is_callable($callback)) {
            throw new Exception('Bad callback function');
        }

        $priority = 9;
        if (($pos=(strpos($pattern, self::DELIMITER)))!==false) {
            $strSc = substr($pattern, 0 ,$pos);
            switch ($strSc) {
                case self::SC_FULL:
                    $scheme = self::SC_FULL;
                    $priority = 0;
                    break;
                case self::SC_REG:
                    $scheme = self::SC_REG;
                    $priority = 6;
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

        if ($scheme === self::SC_STR && $pattern[0] === '^') {
            $priority = 3;
            $pattern = substr($pattern, 1);
        }

        $urlParams = ['pattern'=>$pattern, 'scheme'=>$scheme, 'callback' => $callback, 'priority' => $priority];
        if (!is_null($args)) {
            $urlParams['args'] = $args;
        }

        $this->urls[$method][$pattern] = $urlParams;
    }

    public function testUrl($url, $pattern, $scheme)
    {
        switch ($scheme) {
            case self::SC_FULL:
                return $url === $pattern;
                break;
            case self::SC_STR:
                return (strpos($url, $pattern) !== false);
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

    //если 0 - сразу
    // если 3  - наибольший
    // если 6  - по порядку
    // если 9 - наибольший
    //TODO implement this!
    public function chooseUrl(array $urls)
    {
        ksort($urls);
        foreach ($urls as $priority=>$urlsIntrList) {
            ///
        }

    }

    //TODO add prefix to str ^ - stop prefix search on this prefix
    public function run()
    {
        if ($this->isRun) { return; }
        $this->isRun = true;

        $methods  = [self::METHOD_ALL, $_SERVER['REQUEST_METHOD']];
        $strUrl = $this->getUrlNormal();


        $suitableUrls = [];
        $topPr = 99;
        foreach ($methods as &$method) {
            $currUrls = empty($this->urls[$method]) ? [] : $this->urls[$method];
            foreach ($currUrls as $urlData) {
                if ($urlData['priority'] > $topPr) {
                    continue;
                }
                if ($this->testUrl($strUrl, $urlData['pattern'], $urlData['scheme'])) {
                    $topPr = $urlData['priority'];
                    if ($urlData['priority'] === 0) {
                        $suitableUrls = [$urlData['priority'] => [$urlData]];
                        break;
                    }
                    $suitableUrls[$urlData['priority']][] = $urlData;
                }
            }
        }

        //TODO more good 404
        $urlData = $this->chooseUrl($suitableUrls);
        if (empty($urlData)) {
            echo $this->exception404();
            return;
        }

        if (isset($urlData['args'])) {
            return call_user_func_array($urlData['callback'], $urlData['args']);
        } else {
            return call_user_func($urlData['callback']);
        }
    }

    function __invoke()
    {
        return $this->run();
    }

    public function exception404()
    {
        if (headers_sent()) {
            throw new \LogicException('Headers already send, url no found');
        }
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        header("Status: 404 Not Found");
        $_SERVER['REDIRECT_STATUS'] = 404;
        return "<h1>Not Found</h1>";
    }


}
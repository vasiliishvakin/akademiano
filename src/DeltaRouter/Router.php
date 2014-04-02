<?php
namespace  DeltaRouter;

use DeltaRouter\Exception\NotFoundException;
use HttpWarp\Request;

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

    /**
     * @var Request
     */
    protected $request;

    function __construct(Request $request = null)
    {
        if (!is_null($request)) {
            $this->setRequest($request);
        }
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if (is_null($this->request)) {
            $this->request = new Request();
        }
        return $this->request;
    }

    public function setUrl($pattern, $callback, $method = self::METHOD_ALL, $args = null)
    {
        if (!is_callable($callback)) {
            throw new \Exception('Bad callback function');
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
                return (strpos($url, $pattern) === 0);
                break;
            case self::SC_REG:
                return (bool) preg_match($pattern, $url);
                break;
            default:
                return false;
        }
    }

    protected function chooseUrlLong(array $urls)
    {
        $max = 0;
        $url = false;
        foreach ($urls as $urlItem) {
            $length = strlen($urlItem['pattern']);
            if ($length > $max) {
                $url = $urlItem;
            }
        }
        return $url;
    }

    protected function chooseUrlFirst(array $urls)
    {
        return reset($urls);
    }

    // если 0 - первый
    // если 3 - длинный
    // если 6 - первый
    // если 9 - длинный
    public function chooseUrl(array $urls)
    {
        ksort($urls);
        foreach ($urls as $priority=>$urlList) {
            switch ($priority) {
                case 0:
                case 6:
                    $url = $this->chooseUrlFirst($urlList);
                    if ($url) {
                        return $url;
                    }
                    break;
                case 3:
                case 9:
                    $url = $this->chooseUrlLong($urlList);
                    if ($url) {
                        return $url;
                    }
                    break;
                default:
                    throw new \LogicException("Choose method for url priority $priority not defined");
            }
        }
        return null;
    }

    //TODO add prefix to str ^ - stop prefix search on this prefix
    public function run()
    {
        if (empty($this->urls)) {
            throw new \Exception("In this router urls is not defined");
        }

        if ($this->isRun) { return; } //fix double run
        $this->isRun = true;

        $methods  = [self::METHOD_ALL, $this->getRequest()->getMethod()];
        $strUrl = $this->getRequest()->getUriNormal();

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
            throw new NotFoundException();
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
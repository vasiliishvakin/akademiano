<?php

namespace Akademiano\Core\Controller;

use Akademiano\Config\ConfigurableTrait;
use Akademiano\HttpWarp\Request;
use Akademiano\HttpWarp\Response;
use Akademiano\Router\Route;
use Akademiano\Router\RoutePattern;
use Akademiano\Router\Router;
use Akademiano\SimplaView\ViewInterface;
use Akademiano\Utils\Parts\DIContainerTrait;

class AkademianoController implements ControllerInterface
{
    const PAGE_PARAM_NAME = 'p';

    public const ROUTES_PARAMS = null;

    use ConfigurableTrait;
    use DIContainerTrait;

    /** @var  Request */
    private $request;

    /** @var  Response */
    private $response;

    /** @var  ViewInterface */
    private $view;

    /** @var  Router */
    private $router;

    /** @var Route */
    private $route;

    /** @var array */
    private $arguments;

    private $autoRender = true;
    private $autoSend = true;
    private $onlyJson = false;

    public function __construct(Request $request, Response $response, ViewInterface $view, Router $router, Route $route, array $arguments)
    {
        $this->setRequest($request);
        $this->setResponse($response);
        $this->setView($view);
        $this->setRouter($router);
        $this->setRoute($route);
        $this->setArguments($arguments);
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param Router $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * @param Route $route
     */
    public function setRoute(Route $route): void
    {
        $this->route = $route;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ViewInterface $view
     */
    public function setView(ViewInterface $view)
    {
        $this->view = $view;
    }

    /**
     * @return ViewInterface
     */
    public function getView()
    {
        return $this->view;
    }

    public function autoRenderOff()
    {
        $this->autoRender = false;
    }

    public function autoRenderOn()
    {
        $this->autoRender = true;
    }

    public function isAutoRender()
    {
        return $this->autoRender;
    }

    public function autoSendOff()
    {
        $this->autoSend = false;
    }

    public function autoSendOn()
    {
        $this->autoSend = true;
    }

    public function isAutoSend()
    {
        return $this->autoSend;
    }

    public function getControllerName()
    {
        $class = get_class($this);
        $class = explode("\\", $class);
        $class = $class[count($class)-1];
        $class = substr($class, 0, -10);
        $class = lcfirst($class);
        return $class;
    }

    public function getModuleName()
    {
        $class = get_class($this);
        $class = explode("\\", $class);
        $module = $class[0];
        return $module === "Controller" ? null : $module;
    }

    public function setViewTemplate($template)
    {
        if(strpos($template, "/") === false) {
            $controller = $this->getControllerName();
            $module = $this->getModuleName();
            $template = $module ? "{$module}/{$controller}/{$template}" : "{$controller}/{$template}";
        }
        $this->getView()->setTemplate($template);
    }

    public function getPage()
    {
        return (integer) $this->getRequest()->getParam(self::PAGE_PARAM_NAME, 1);
    }

    public function redirect($routeId, array $params = [])
    {
        $url = $this->getRouteUrl($routeId, $params);

        $this->getResponse()->setRedirectUrl($url);
    }

    public function redirectToUrl($url)
    {
        $this->getResponse()->setRedirectUrl($url);
    }

    public function init() {return;}

    public function finalize() {return;}

    public function getRouteUrl($routeId, array $params = [])
    {
        return $this->getRouter()->getUrl($routeId, $params);
    }

    public function getUrlParams()
    {
        //TODO FIX
        $arguments = $this->getArguments();
        $urlParams = [];
        if (isset($arguments[0])) {
            $urlParams = $arguments[0];
        }
        $urlParams = array_merge($this->getRequest()->getParams(), $urlParams);
        return $urlParams;
    }

    /**
     * @return bool
     */
    public function isOnlyJson(): bool
    {
        return $this->onlyJson;
    }

    /**
     * @param bool $onlyJson
     */
    public function setOnlyJson(bool $onlyJson = true): void
    {
        $this->onlyJson = $onlyJson;
    }

    protected static function buildRouteData(string $controllerId, string $patternValue, string $actionId, int $patternType = RoutePattern::TYPE_DEFAULT, ?array $valueParams = null): array
    {
        if (empty($valueParams)) {
            $valueParams = defined('static::ROUTES_PARAMS') ? static::ROUTES_PARAMS : null;
        }
        if (!empty($valueParams)) {
            $patternValue = vsprintf($patternValue, $valueParams);
        }

        return [
            "patterns" => [
                "type" => $patternType,
                "value" => $patternValue,
            ],
            "action" => [$controllerId, $actionId],
        ];
    }

    public function forward(string $routerId)
    {
        $this->autoRenderOff();
        $this->autoSendOff();
        return $this->getRouter()->execById($routerId);
    }
}

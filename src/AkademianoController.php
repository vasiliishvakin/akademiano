<?php

namespace Akademiano\Core\Controller;

use Akademiano\Config\ConfigurableTrait;
use Akademiano\HttpWarp\Request;
use Akademiano\HttpWarp\Response;
use Akademiano\Router\Router;
use Akademiano\SimplaView\ViewInterface;
use Akademiano\Utils\Parts\DIContainerTrait;

class AkademianoController implements ControllerInterface
{
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

    private $autoRender = true;

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
        return (integer) $this->getRequest()->getParam("p", 1);
    }

    public function redirect($url)
    {
        $this->getResponse()->redirect($url);
    }

    public function init() {return;}

    public function finalize() {return;}

    public function getRouteUrl($routeId, array $params = [])
    {
        return $this->getRouter()->getUrl($routeId, $params);
    }
}

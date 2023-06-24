<?php


namespace Akademiano\Core\Controller;

use Akademiano\Config\Config;
use Akademiano\Config\ConfigurableInterface;
use Akademiano\HttpWarp\Request;
use Akademiano\HttpWarp\Response;
use Akademiano\SimplaView\View;
use Akademiano\SimplaView\ViewInterface;
use Akademiano\Utils\DIContainerIncludeInterface;

interface ControllerInterface extends DIContainerIncludeInterface, ConfigurableInterface
{

    /**
     * @param Request $request
     */
    public function setRequest(Request $request);

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @param Response $response
     */
    public function setResponse(Response $response);

    /**
     * @return Response
     */
    public function getResponse();

    /**
     * @param null $path
     * @param null $default
     * @return Config|mixed
     */
    public function getConfig($path = null, $default = null);

    /**
     * @param ViewInterface $view
     */
    public function setView(ViewInterface $view);

    /**
     * @return ViewInterface
     */
    public function getView();

    public function autoRenderOff();

    public function autoRenderOn();

    public function isAutoRender();

    public function autoSendOff();

    public function autoSendOn();

    public function isAutoSend();

    public function getControllerName();

    public function getModuleName();

    public function setViewTemplate($template);

    public function getPage();

    public function redirect($url);

    public function init();

    public function finalize();

    public function getRouteUrl($routeId, array $params = []);

    public function isOnlyJson(): bool;

    public function setOnlyJson(bool $onlyJson = true): void;
}

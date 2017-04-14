<?php
namespace Akademiano\Core;

use Akademiano\Acl\Model\AclManager;
use Akademiano\Acl\RestrictedControllerInterface;
use Akademiano\Config\Config;
use Akademiano\Config\ConfigInterface;
use Akademiano\Config\ConfigLoader;
use Akademiano\Utils\DIContainerIncludeInterface;
use Akademiano\Utils\Exception\DIContainerAlreadyExistsServiceException;
use Akademiano\HttpWarp\Environment;
use Akademiano\Router\Exception\NotFoundException;
use Akademiano\Entity\UserInterface;
use Akademiano\Utils\Object\Prototype\ArrayableInterface;
use Akademiano\Utils\Parts\DIContainerTrait;
use Composer\Autoload\ClassLoader;
use Akademiano\Core\Exception\AccessDeniedException;
use Akademiano\Router\Route;
use Akademiano\Router\Router;
use Akademiano\SimplaView\ViewInterface;
use Akademiano\HttpWarp\Exception\HttpUsableException;
use Akademiano\HttpWarp\Request;
use Akademiano\HttpWarp\Response;
use Akademiano\HttpWarp\Session;
use Akademiano\Core\Controller\ControllerInterface;
use Pimple\Container;


class Application implements ConfigInterface, DIContainerIncludeInterface
{
    const CONFIG_NAME_RESOURCES = "resources";
    const CONFIG_NAME_ROUTES = "routes";

    const CONFIG_LEVEL_APP = 100;
    const CONFIG_LEVEL_PROJECT = 200;
    const CONFIG_LEVEL_SITE = 300;

    use DIContainerTrait;
    use ApplicationBaseComponentsTrait;

    /** @var  bool */
    protected $initialized = false;

    public function __construct()
    {
        if (!defined('ROOT_DIR')) {
            $rootDir = realpath(__DIR__ . '/../../../../../');
            define('ROOT_DIR', $rootDir);
        }
    }

    private function getDiContainerRaw()
    {
        if (null === $this->diContainer) {
            $this->diContainer = new DI();
        }
        return $this->diContainer;
    }

    private function addToDiContainer($id, $value)
    {
        $di = $this->getDiContainerRaw();
        if (isset($di[$id])) {
            throw new DIContainerAlreadyExistsServiceException(sprintf('Cannot override exist service "%s".', $id));
        }
        $di[$id] = $value;
    }

    /**
     * @return Container
     */
    public function getDiContainer()
    {
        $di = $this->getDiContainerRaw();
        if (!isset($this->getDiContainerRaw()["applicationComponents"])) {
            $di->register(new ApplicationComponentsProvider());
        }
        return $di;
    }

    /**
     * @return boolean
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * @param boolean $initialized
     */
    public function setInitialized($initialized = true)
    {
        $this->initialized = $initialized;
    }

    public function setLoader(ClassLoader $loader)
    {
        $this->addToDiContainer("loader", function () use ($loader) {
            return $loader;
        });
    }

    public function setModuleManager(ModuleManager $moduleManager)
    {
        $this->addToDiContainer("moduleManager", function () use ($moduleManager) {
            return $moduleManager;
        });
    }

    /**
     * @return ModuleManager
     */
    public function getModuleManager()
    {
        return $this->getDiContainer()["moduleManager"];
    }

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment)
    {
        $this->addToDiContainer("environment", function () use ($environment) {
            return $environment;
        });
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->addToDiContainer("request", function () use ($request) {
            return $request;
        });
    }

    /**
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->addToDiContainer("session", function () use ($session) {
            return $session;
        });
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->addToDiContainer("response", function () use ($response) {
            return $response;
        });
    }

    /**
     * @param ViewInterface $view
     */
    public function setView(ViewInterface $view)
    {
        $this->addToDiContainer("view", function () use ($view) {
            return $view;
        });
    }

    public function setRouter(Router $router)
    {
        $this->addToDiContainer("router", function () use ($router) {
            return $router;
        });
    }

    /**
     * @param ConfigLoader $configLoader
     */
    public function setConfigLoader(ConfigLoader $configLoader)
    {
        $this->addToDiContainer("configLoader", function () use ($configLoader) {
            return $configLoader;
        });
    }

    /**
     * @return ConfigLoader
     */
    public function getConfigLoader()
    {
        return $this->getDiContainer()["configLoader"];
    }

    /**
     * @param null $path
     * @param null $default
     * @return Config
     */
    public function getConfig($path = null, $default = null)
    {
        /** @var Config $config */
        $config = $this->getDiContainer()["config"];
        if (null !== $path) {
            return $config->get($path, $default);
        }
        return $config;
    }

    /**
     * @return Config
     */
    public function getRoutes()
    {
        return $this->getDiContainer()["routes"];
    }

    /**
     * @return Config
     */
    public function getResources()
    {
        return $this->getDiContainer()["resources"];
    }

    public function initRoute($route, $name = null)
    {
        $route = Route::normalize($route);
        if (is_array($route["action"])) {
            $route["action"] = array_values($route["action"]);
            $route["args"] = isset($route["args"]) ? array_merge($route["action"], [$route["args"]]) : $route["action"];
            $route["action"] = [$this, 'action'];
        }
        $this->getRouter()->setRoute($route, $name);
    }

    public function initRoutes($routes)
    {
        foreach ($routes as $name => $route) {
            $this->initRoute($route, $name);
        }

        return true;
    }

    public function initResources($resources)
    {
        foreach ($resources as $name => $value) {
            $this->getDiContainer()[$name] = $value;
        }
    }

    public function getErrorFunction($errorCode)
    {
        $closure = $this->getConfig(["errors", $errorCode]);
        if ($closure instanceof Config) {
            $closure = $closure->toArray();
        }

        return $closure;
    }

    public function catchRunException(\Exception $e)
    {
        $errorCode = $e->getCode();
        $closure = $this->getErrorFunction($errorCode);
        if (is_array($closure)) {
            return $this->action($closure[0], $closure[1]);
        } elseif (is_callable($closure)) {
            return call_user_func($closure);
        } elseif ($errorCode === 404) {
            return $this->getResponse()->error404();
        } else {
            throw $e;
        }
    }

    public function init($reinitialize = false)
    {
        if ($this->isInitialized() && !$reinitialize) {
            return;
        }
        $this->setInitialized(true);

        $resources = $this->getResources();
        $this->initResources($resources);

        $routes = $this->getRoutes();
        $this->initRoutes($routes);

        /** @var \Closure[] $initClosures */
        $initClosures = $this->getConfig("init", [])->toArray();
        foreach ($initClosures as $initClosure) {
            if (is_callable($initClosure)) {
                call_user_func($initClosure, $this);
            }
        }

        $mm = $this->getModuleManager();
        $mm->load($this);
    }

    public function run()
    {
        $this->init();

        try {
            $this->getRouter()->run();
        } catch (HttpUsableException $e) {
            $this->catchRunException($e);
        }
    }

    public function action($controllerInfo, $action, ...$arguments)
    {
        $actionName = lcfirst($action);
        $action = $actionName . 'Action';

        $view = $this->getView();
        $httpCachePath = null;

        $controllerPath = null;

        if (is_array($controllerInfo)) {
            $module = ucfirst($controllerInfo["module"]);
            $controllerId = lcfirst($controllerInfo["controller"]);
            $controllerName = ucfirst($controllerInfo["controller"]);
            $controllerPath = "{$module}\\Controller\\" . $controllerName . 'Controller';
            $template = $module . DIRECTORY_SEPARATOR . $controllerId . DIRECTORY_SEPARATOR . $actionName;
        } else {
            $possibleControllers = [];
            $controllerId = lcfirst($controllerInfo);
            $controllerName = ucfirst($controllerInfo);
            if ($this["isCurrentSiteDirDefault"]) {
                $possibleControllers[] = "Sites\\_Default\\Controller\\" . $controllerName . 'Controller';
            } else {
                $currentSite = $this["currentSite"];
                $currentSiteNS = ucfirst(str_replace(".", "_", $currentSite));
                $possibleControllers[] = "Sites\\" . $currentSiteNS . "\\Controller\\" . $controllerName . 'Controller';
            }

            $possibleControllers[] = "Sites\\All\\Controller\\" . $controllerName . 'Controller';
            $possibleControllers[] = "Akademiano\\App\\Controller\\" . $controllerName . 'Controller';

            foreach ($possibleControllers as $pController) {
                if (class_exists($pController)) {
                    $controllerPath = $pController;
                    break;
                }
            }
            $template = $controllerId . DIRECTORY_SEPARATOR . $actionName;
        }

        if (null === $controllerPath) {
            throw new NotFoundException("Controller not found");
        }

        $controller = new $controllerPath();
        if (!$controller instanceof ControllerInterface) {
            throw new \ErrorException("Controller mast be implement ControllerInterface");
        }
        $controller->setDIContainer($this->getDiContainer());
        $controller->setRequest($this->getRequest());
        $response = $this->getResponse();

        if (!empty($httpCachePath)) {
            array_unshift($httpCachePath, "HttpCache");
            $httpCacheParams = $this->getConfig($httpCachePath, [])->toArray();
            $response->setDefaults($httpCacheParams);
        }
        $controller->setResponse($response);

        if (!$view->exist($template)) {
            if ($actionName === "add" || $actionName === "edit") {
                $template2 = "{$controllerName}/form";
                if ($view->exist($template2)) {
                    $template = $template2;
                }
            }
        }
        $view->setTemplate($template);
        $view->assignArray([
            '_controller' => $controllerId,
            '_action' => $actionName,
            '_path' => $controllerId . '/' . $actionName
        ]);
        $controller->setView($view);

        if ($controller instanceof RestrictedControllerInterface) {
            if (!$controller->checkAccess()) {
                throw new AccessDeniedException();
            }
        } elseif (!$this->isAllow()) {
            throw new AccessDeniedException();
        }

        $controller->init();
        //prepare arguments (merge arrays from args aon route params in one)
        if (!empty($arguments)) {
            $arguments = array_merge(...$arguments);
        } else {
            $arguments = [];
        }
        $result = $controller->$action($arguments);
        $controller->finalize();

        if (is_array($result)) {
            $controller->getView()->assignArray($result);
        }

        if ($controller->isAutoRender()) {
            $html = $controller->getView()->render();
            $response = $controller->getResponse();
            $response->setBody($html);
            $response->sendReplay();
        }
    }

    public function isAllow($resource = null, UserInterface $user = null)
    {
        $di = $this->getDiContainer();
        if (isset($di["aclManager"])) {
            /** @var AclManager $aclManager */
            $aclManager = $di['aclManager'];
            if (!$resource) {
                /** @var Request $request */
                $request = $di['request'];
                $resource = (string)$request->getUrl()->getPath();
            }
            if (!$user) {
                $user = $aclManager->getCustodian()->getCurrentUser();
            }

            return $aclManager->isAllow($resource, $user);
        }

        return true;
    }
}

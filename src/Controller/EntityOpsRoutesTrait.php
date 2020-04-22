<?php


namespace Akademiano\EntityOperator\Ext\Controller;


use Akademiano\EntityOperator\Ext\EntityOpsRoutesStore;
use Akademiano\Router\RoutePattern;
use Akademiano\SimplaView\ViewInterface;
use Akademiano\Utils\StringUtils;

trait EntityOpsRoutesTrait
{
    /** @var  EntityOpsRoutesStore */
    protected $entityOpsRoutesStore;

    /**
     * @return ViewInterface
     */
    abstract public function getView();

    abstract protected static function buildRouteData(string $controllerId, string $patternValue, string $actionId, int $patternType = RoutePattern::TYPE_DEFAULT, ?array $valueParams = null): array;

    /**
     * @return EntityOpsRoutesStore
     */
    public function getEntityOpsRoutesStore()
    {
        if (null === $this->entityOpsRoutesStore) {
            $class = static::ENTITY_OPSR_STORE_CLASS;
            $this->entityOpsRoutesStore = new $class();
        }
        return $this->entityOpsRoutesStore;
    }

    public function init(): void
    {
        if (!empty($routes = $this->getEntityOpsRoutesStore()->toArray())) {
            $this->getView()->assignArray($routes);
        }
    }

    public static function buildRoutes()
    {
        $routesStoreClass = static::ENTITY_OPSR_STORE_CLASS;
        /** @var EntityOpsRoutesStore $routes */
        $routes = (new $routesStoreClass);
        $class = StringUtils::cutClassName(get_called_class());
        $controllerId = lcfirst(mb_substr($class, 0, -strlen('Controller')));

        $routes = [
            $routes->getListRoute() => static::buildRouteData($controllerId, '/%1$s', "list", RoutePattern::TYPE_FULL),
            $routes->getViewRoute() => static::buildRouteData($controllerId, '^/%1$s/id(?P<id>\w+)$', "view", RoutePattern::TYPE_REGEXP),
            $routes->getAddRoute() => static::buildRouteData($controllerId, '/%1$s/add', "form", RoutePattern::TYPE_PREFIX),
            $routes->getEditRoute() => static::buildRouteData($controllerId, '^/%1$s/id(?P<id>\w+)/edit$', "form", RoutePattern::TYPE_REGEXP),
            $routes->getSaveRoute() => static::buildRouteData($controllerId, '^/%1$s/save$', "save", RoutePattern::TYPE_REGEXP),
            $routes->getDeleteRoute() => static::buildRouteData($controllerId, '^/%1$s/(?P<id>\w+)/delete$', "delete", RoutePattern::TYPE_REGEXP),
        ];
        return $routes;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 30.10.2015
 * Time: 16:41
 */

namespace DeltaTwigExt;


use DeltaRouter\Router;

class UrlExtension extends \Twig_Extension
{
    /** @var  \Callable */
    protected $routeGenerator;

    public function getName()
    {
        return 'delta_url';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'route_url',
                [$this, 'routeUrl'],
                [
//                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * @return Callable
     */
    public function getRouteGenerator()
    {
        return $this->routeGenerator;
    }

    /**
     * @param Callable $routeGenerator
     */
    public function setRouteGenerator($routeGenerator)
    {
        $this->routeGenerator = $routeGenerator;
    }

    public function routeUrl($routeId, array $params = [])
    {
        return call_user_func($this->getRouteGenerator(), $routeId, $params);
    }

}

<?php

namespace Akademiano\Twig\Extensions;

class UrlExtension extends \Twig_Extension
{
    /** @var  \Callable */
    protected $routeGenerator;

    public function getName()
    {
        return 'akademiano_url';
    }

    /**
     * @return array
     * add is need is_safe' => ['html'],
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'route_url',
                [$this, 'routeUrl'],
                [
                ]
            ),
            new \Twig_SimpleFunction(
                'url',
                [$this, 'routeUrl'],
                [
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

<?php

namespace Akademiano\Twig\Extensions;

use Akademiano\Utils\ArrayTools;
use Akademiano\Utils\Object\Prototype\ArrayableInterface;

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
                'url',
                [$this, 'routeUrl'],
                [
                ]
            ),
            new \Twig_SimpleFunction(
                'object_url',
                [$this, 'objectUrl'],
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

    public function objectUrl($routeId, $object = null, array $params = null)
    {
        $newParams = [];
        if (null !== $object) {
            if (is_object($object)) {
                if($object instanceof ArrayableInterface) {
                    $newParams = $object->toArray();
                } else {
                    $newParams = (array) $object;
                }
            } else {
                throw new \LogicException('Object mast be a object');
            }
        }
        if (null !== $params) {
            $newParams = ArrayTools::mergeRecursive($newParams, $params);
        }
        return $this->routeUrl($routeId, $newParams);
    }
}

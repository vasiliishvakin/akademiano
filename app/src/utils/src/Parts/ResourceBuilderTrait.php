<?php


namespace Akademiano\Utils\Parts;

use Akademiano\Utils\DIContainerIncludeInterface;
use Pimple\Container;

trait ResourceBuilderTrait
{
    public static function getBuilder(): \Closure
    {
        $class = get_called_class();
        if (method_exists($class, "build")) {
            return function (Container $c) {
                return static::build($c);
            };
        } else {
            return function (Container $c) {
                $class = get_called_class();
                $object = new $class();
                if ($object instanceof DIContainerIncludeInterface) {
                    $object->setDiContainer($c);
                }
                return $object;
            };
        }
    }
}

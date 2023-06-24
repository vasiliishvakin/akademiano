<?php


namespace Akademiano\Utils;

use Pimple\Container;

/**
 * Interface ResourceBuilderInterface
 * @package Akademiano\Utils
 * @method static build(Container $container): object
 */
interface ResourceBuilderInterface
{
    public static function getBuilder(): \Closure;
}

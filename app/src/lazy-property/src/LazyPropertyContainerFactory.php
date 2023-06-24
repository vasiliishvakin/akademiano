<?php


namespace Akademiano\LazyProperty;


class LazyPropertyContainerFactory
{
    private static LazyPropertyContainer $container;

    static public function factory(): LazyPropertyContainer
    {
        if (!isset(self::$container)) {
            self::$container = new LazyPropertyContainer();
        }
        return self::$container;
    }

    static public function build(): LazyPropertyContainer
    {
       return new LazyPropertyContainer();
    }
}

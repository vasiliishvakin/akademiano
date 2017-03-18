<?php


namespace Akademiano\Core;


use Pimple\Container;

interface ModuleInterface
{
    public function init(ModuleManager $moduleManager, Container $diContainer);

}

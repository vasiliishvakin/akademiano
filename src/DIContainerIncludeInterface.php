<?php


namespace Akademiano\Utils;


use Pimple\Container;

interface DIContainerIncludeInterface
{
    public function setDiContainer(Container $diContainer);

    /**
     * @return Container
     */
    public function getDiContainer();

}

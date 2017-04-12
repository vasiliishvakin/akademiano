<?php


namespace Akademiano\Utils\Parts;



use Pimple\Container;

trait DIContainerTrait
{
    /** @var  Container */
    protected $diContainer;

    public function setDiContainer(Container $diContainer)
    {
        $this->diContainer = $diContainer;
    }

    /**
     * @return Container
     */
    public function getDiContainer()
    {
        return $this->diContainer;
    }
}

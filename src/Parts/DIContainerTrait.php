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
        if (null === $this->diContainer) {
            $this->diContainer = new Container();
        }
        return $this->diContainer;
    }
}

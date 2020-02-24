<?php


namespace Akademiano\Operator;


use Akademiano\Config\Config;
use Akademiano\Config\ConfigInterface;
use Akademiano\Config\ConfigurableInterface;
use Akademiano\Config\ConfigurableTrait;
use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Delegating\IncludeOperatorInterface;
use Akademiano\Delegating\IncludeOperatorTrait;
use Akademiano\Delegating\OperatorInterface;
use Akademiano\Utils\DIContainerIncludeInterface;
use Akademiano\Utils\Parts\DIContainerTrait;
use Pimple\Container;

class WorkersContainer extends \Akademiano\DI\Container implements ConfigurableInterface, IncludeOperatorInterface, DIContainerIncludeInterface
{
    use ConfigurableTrait {
        getConfig as private configurableGetConfig;
    }

    use IncludeOperatorTrait {
        getOperator as private delegatingGetOperator;
    }

    use DIContainerTrait;

    public function __construct(array $values = [], Container $diContainer = null)
    {
        if (null !== $diContainer) {
            $this->setDiContainer($diContainer);
        }
        parent::__construct($values);
    }

    /**
     * @param Container $dependencies
     * @deprecated
     */
    public function setDependencies(Container $dependencies)
    {
        $this->setDiContainer($dependencies);
    }

    /**
     * @return Container
     * @deprecated
     */
    public function getDependencies(): Container
    {
        return $this->getDiContainer();
    }

    public function getConfig($path = null, $default = null)
    {
        if (null === $this->config) {
            if (isset($this->getDependencies()[ConfigInterface::RESOURCE_ID])) {
                $this->config = $this->getDiContainer()[ConfigInterface::RESOURCE_ID];
            } else {
                $this->config = new Config([], $this->getDependencies());
            }
        }
        return $this->configurableGetConfig($path, $default);
    }

    public function getOperator(): ?OperatorInterface
    {
        if (null === $this->operator) {
            $this->operator = $this->getDependencies()[OperatorInterface::RESOURCE_ID];
        }
        return $this->operator;
    }

    public function prepare($value)
    {
        if ($value instanceof ConfigurableInterface) {
            $value->setConfig($this->getConfig());
        }
        if ($value instanceof DelegatingInterface) {
            $value->setOperator($this->getOperator());
        }
        return $value;
    }
}

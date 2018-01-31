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
use Pimple\Container;

class WorkersContainer extends \Akademiano\DI\Container implements ConfigurableInterface, IncludeOperatorInterface
{
    use ConfigurableTrait {
        getConfig as private configurableGetConfig;
    }

    use IncludeOperatorTrait {
        getOperator as private delegatingGetOperator;
    }

    public function __construct(array $values = [], Container $dependencies = null)
    {
        if (null !== $dependencies) {
            $this->setDependencies($dependencies);
        }
        parent::__construct($values);
    }


    /** @var  Container */
    protected $dependencies;

    /**
     * @return Container
     */
    public function getDependencies(): Container
    {
        return $this->dependencies;
    }

    /**
     * @param Container $dependencies
     */
    public function setDependencies(Container $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    public function getConfig($path = null, $default = null)
    {
        if (null === $this->config) {
            if (!isset($this->getDependencies()[ConfigInterface::RESOURCE_ID])) {
                $this->config = new Config([], $this->getDependencies());
            }
            $this->config = $this->getDependencies()[ConfigInterface::RESOURCE_ID];
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

<?php


namespace Akademiano\Operator;


use Akademiano\DI\Container;
use Akademiano\Operator\Worker\ConfigurableInterface;

class WorkersContainer extends Container implements WorkersContainerInterface
{
    use IncludeOperatorTrait;

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

    public function getConfig()
    {
        return $this->getDependencies()["config"];
    }

    public function offsetSet($id, $value)
    {
        if (!is_object($value) || !method_exists($value, '__invoke')) {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" does not contain an object definition.', $id));
        }

        $value = function ($c) use ($value, $id) {
            if (is_callable($value)) {
                $result = $value($c);
                if (is_object($result)) {
                    if ($result instanceof IncludeOperatorInterface) {
                        $result->setOperator($this->getOperator());
                    }
                    if ($result instanceof ConfigurableInterface) {
                        $result->addConfig($this->getOperator()->getWorkerParams($id));
                    }
                }
                return $result;
            } else {
                return $value;
            }
        };
        parent::offsetSet($id, $value);
    }
}

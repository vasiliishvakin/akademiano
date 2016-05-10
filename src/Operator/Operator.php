<?php


namespace EntityOperator\Operator;


use DeltaUtils\ArrayUtils;
use EntityOperator\Worker\WorkerInterface;
use EntityOperator\Command\CommandInterface;
use Pimple\Container;

class Operator implements OperatorInterface
{
    /** @var  Container */
    protected $workers;
    protected $actionMap = [];
    /** @var  Container */
    protected $dependencies;

    /**
     * @return Container
     */
    public function getWorkers()
    {
        if (null === $this->workers) {
            $this->workers = new Container();
            $this->workers["operator"] = $this;
        }
        return $this->workers;
    }

    /**
     * @return Container
     */
    public function getDependencies()
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

    /**
     * @return array
     */
    public function getActionMap()
    {
        return $this->actionMap;
    }

    public function addAction($action, $class = null, $worker)
    {
        $path = $class !== null ? [$action, $class] : [$action, ""];
        $this->actionMap = ArrayUtils::set($this->actionMap, $path, $worker);
    }

    public function getWorker($action, $class = null)
    {
        $pathArray = [];
        if (null !== $class) {
            $pathArray[] = [$action, $class];
        }
        $pathArray[] = [$action, ""];

        foreach ($pathArray as $path) {
            if (ArrayUtils::issetByPath($this->actionMap, $path)) {
                $worker = ArrayUtils::get($this->actionMap, $path);
                break;
            }
        }
        if (!isset($worker)) {
            return;
        }
        $worker = $this->getWorkers()[$worker];
        return $worker;
    }

    public function execute(CommandInterface $command)
    {
        //send message

        $worker = $this->getWorker($command->getName(), $command->getClass());
        $result = $worker->execute($command);

        //send message

        return $result;
    }

}

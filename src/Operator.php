<?php


namespace Akademiano\Operator;

use Akademiano\Config\Config;
use Akademiano\Operator\Exception\NotFoundSuitableWorkerException;
use Akademiano\Utils\ArrayTools;
use Akademiano\Operator\Command\AfterCommand;
use Akademiano\Operator\Command\CommandChainElementInterface;
use Akademiano\Operator\Command\CommandFinallyInterface;
use Akademiano\Operator\Command\PreAfterCommandInterface;
use Akademiano\Operator\Command\PreCommand;
use Akademiano\Operator\Worker\Exception\BreakException;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Operator\Worker\Exception\TryNextException;
use Pimple\Container;


class Operator implements OperatorInterface
{
    const CLAS_MAP_FILE_NAME = 'operator.class.map';

    /** @var  Container */
    protected $workers;
    protected $actionMap = [];
    protected $workersParams = [];
    protected $workerTables = [];

    /** @var  Container */
    protected $dependencies;

    /** @var  Config */
    protected $classMap;

    /**
     * @return Container
     */
    public function getWorkers()
    {
        if (null === $this->workers) {
            $this->workers = new WorkersContainer();
            $this->workers->setDependencies($this->getDependencies());
            $this->workers->setOperator($this);
        }
        return $this->workers;
    }

    public function addWorker($name, Callable $worker)
    {
        $this->getWorkers()[$name] = $worker;
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

    public function getDependency($name)
    {
        return $this->getDependencies()[$name];
    }

    /**
     * @return array
     */
    public function getActionMap()
    {
        return $this->actionMap;
    }

    public function addAction($action, $workerName, $class = null, $order = 0)
    {
        $path = $class !== null ? [$action, $class] : [$action, ""];
        $this->actionMap = ArrayTools::add($this->actionMap, $path, ["name" => $workerName, "order" => $order]);
    }

    public function setWorkerTable($tableId, $workerName)
    {
        $this->workerTables[$tableId] = $workerName;
    }

    public function getWorkerByTable($tableId)
    {
        if (!isset($this->workerTables[$tableId])) {
            return null;
        }
        $workerName = $this->workerTables[$tableId];
        return $this->getWorker($workerName);
    }

    public function getTableIdByWorker($worker)
    {
        $workers = array_flip($this->workerTables);
        if (!isset($workers[$worker])) {
            return null;
        }
        return $workers[$worker];
    }

    public function getWorkerParams($workerName = null, $paramName = null)
    {
        if (null === $workerName) {
            return $this->workersParams;
        }
        $params = isset($this->workersParams[$workerName]) ? $this->workersParams[$workerName] : [];
        if (null === $paramName) {
            return $params;
        }
        $value = isset($params[$paramName]) ? $params[$paramName] : null;
        return $value;
    }

    public function setWorkerParams($workerName, array $workersParams)
    {
        $this->workersParams[$workerName] = $workersParams;
    }

    public function getWorker($workerName)
    {
        return $this->getWorkers()[$workerName];
    }

    /**
     * @param CommandInterface $command
     * @return WorkerInterface[]
     */
    public function getCommandWorkers(CommandInterface $command)
    {
        $class = (string)$command->getClass();
        $action = $command->getName();

        do {
            if (false === $class) {
                $class = "";
            }
            $path = [$action, $class];
            if (ArrayTools::issetByPath($this->actionMap, $path)) {
                $workers = ArrayTools::get($this->actionMap, $path);
                $workers = ArrayTools::sortByKey($workers);
                //may be cache like: $this->actionMap = ArrayUtils::set($this->actionMap, $path, $workers);
                foreach ($workers as $worker) {
                    yield $this->getWorker($worker["name"]);
                }
            }
            if ("" !== $class) {
                $class = get_parent_class($class);
            }
        } while ("" !== $class);
    }

    public function preExecute(CommandInterface $command)
    {
        $preCommand = new PreCommand($command);
        $this->execute($preCommand);
        $command = $preCommand->extractParentCommand();
        return $command;
    }

    public function afterExecute(CommandInterface $command, $result)
    {
        if (!$result instanceof \SplStack) {
            $stack = new \SplStack();
            $stack->push($result);
            $result = $stack;
        }
        $afterCommand = new AfterCommand($command, $result);
        $this->execute($afterCommand);
        $result = $afterCommand->extractResult();
        return $result;
    }

    public function getClassMap(): Config
    {
        return $this->classMap;
    }

    public function setClassMap(Config $classMap)
    {
        $this->classMap = $classMap;
    }

    public function mapCommandParamClass(string $class):string
    {
        $classMap = $this->getClassMap();
        if (isset($classMap[$class])) {
            return $classMap[$class];
        } else {
            return $class;
        }
    }


    public function execute(\Akademiano\Delegating\Command\CommandInterface $command)
    {
        //prepare action
        if (!$command instanceof PreAfterCommandInterface) {
            $command->setClass($this->mapCommandParamClass($command->getClass()));

            $command = $this->preExecute($command);
        }

        $result = null;
        $break = !($command instanceof CommandChainElementInterface) || ($command instanceof CommandFinallyInterface);
        $workersCount = 0;
        foreach ($this->getCommandWorkers($command) as $worker) {
            try {
                $workersCount++;
                $result = $worker->execute($command);
            } catch (TryNextException $e) {
                $break = false;
            } catch (BreakException $e) {
                $break = true;
            }
            if ($break) {
                break;
            }
        }
        if (!$command instanceof PreAfterCommandInterface && $workersCount === 0) {
            throw  new NotFoundSuitableWorkerException(sprintf('Empty workers for command "%s" and class "%s"', $command->getName(), $command->getClass()));
        }
        //after execute
        if (!$command instanceof PreAfterCommandInterface) {
            $result = $this->afterExecute($command, $result);
        }
        return $result;
    }
}

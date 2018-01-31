<?php


namespace Akademiano\Operator;

use Akademiano\Config\Config;
use Akademiano\Delegating\OperatorInterface;
use Akademiano\Operator\Command\OperatorSpecialCommandInterface;
use Akademiano\Operator\Command\PreCommandInterface;
use Akademiano\Operator\Command\SubCommand;
use Akademiano\Operator\Command\SubCommandInterface;
use Akademiano\Operator\Command\WorkerInfoCommand;
use Akademiano\Operator\Exception\NotFoundSuitableWorkerException;
use Akademiano\Operator\Exception\OperatorException;
use Akademiano\Operator\Worker\WorkerSelfInstancedInterface;
use Akademiano\Operator\Worker\WorkerSelfMapCommandsInterface;
use Akademiano\Operator\WorkersMap\WorkersMap;
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
    const WORKERS_FILE = 'workers';
    const WORKERS_MAP_FILE = 'workers.map';


    /** @var  WorkersContainer */
    protected $workers;

    /** @var  Container */
    protected $dependencies;


    /** @var  WorkersMap */
    protected $workersMap;

    /**
     * Operator constructor.
     * @param Container $dependencies
     */
    public function __construct(Container $dependencies = null)
    {
        if (null !== $dependencies) {
            $this->setDependencies($dependencies);
        }
    }

    public function getWorkers(): WorkersContainer
    {
        if (null === $this->workers) {
            $this->workers = new WorkersContainer();
            $this->workers->setDependencies($this->getDependencies());
        }
        return $this->workers;
    }

    public function addWorker($name, Callable $worker)
    {
        $this->getWorkers()[$name] = $worker;
    }

    public function setWorkers(iterable $workers)
    {
        $this->workers = null;
        foreach ($workers as $name => $callable) {
            if (!is_callable($callable)) {
                if (!is_string($callable)) {
                    throw new OperatorException(sprintf('Worker mast be callable ortring class name, "%s" done', gettype($callable)));
                }
                if (!class_exists($callable)) {
                    throw new OperatorException(sprintf('Worker class "%s" not exist', $callable));
                }
                if (!is_subclass_of($callable, WorkerSelfInstancedInterface::class)) {
                    throw new OperatorException(sprintf('Worker class "%s" not implements "%s"', $callable, WorkerSelfInstancedInterface::class));
                }
                $name = constant($callable . '::WORKER_ID');
                //create mapping
                if (is_subclass_of($callable, WorkerSelfMapCommandsInterface::class)) {
                    $commandsMapping = call_user_func([$callable, WorkerSelfMapCommandsInterface::SELF_MAP_COMMAND_NAME]);
                    if (!empty($commandsMapping)) {
                        $relations = [
                            $name => $commandsMapping,
                        ];
                        $this->getWorkersMap()->addRelations($relations);
                    }
                }
                $name = constant($callable . '::WORKER_ID');
                $callable = \Closure::fromCallable([$callable, WorkerSelfInstancedInterface::SELF_INSTANCE_COMMAND_NAME]);
            }
            $this->addWorker($name, $callable);
        }
        return $this;
    }

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

    public function getWorker($workerId): WorkerInterface
    {
        return $this->getWorkers()[$workerId];
    }

    public function hasWorker($workerId)
    {
        return isset($this->getWorkers()[$workerId]);
    }

    public function getWorkersMap(): WorkersMap
    {
        if (null === $this->workersMap) {
            $this->workersMap = new WorkersMap();
        }
        return $this->workersMap;
    }

    /**
     * @param CommandInterface $command
     * @return \Generator|WorkerInterface
     */
    public function getCommandWorkers(CommandInterface $command): \Generator
    {
        $class = get_class($command);
        do {
            $workersIds = $this->getWorkersMap()->getWorkersIds($class, $command);
            foreach ($workersIds as $workerId) {
                yield $this->getWorker($workerId);
            }
            $class = get_parent_class($class);
        } while ($class && ($class !== SubCommand::class));
    }

    public function preExecute(CommandInterface $command)
    {
        $preCommand = new PreCommand($command);
        $this->execute($preCommand);
        $command = $preCommand->getParentCommand();
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

    public function execute(\Akademiano\Delegating\Command\CommandInterface $command)
    {
        //self commands
        if ($command instanceof OperatorSpecialCommandInterface) {
            return $this->internalExecute($command);
        }

        //prepare action
        if (!$command instanceof SubCommandInterface) {
            $command = $this->preExecute($command);
        }

        //main work
        $result = null;

        $break = (!$command instanceof SubCommandInterface) && (!($command instanceof CommandChainElementInterface) || ($command instanceof CommandFinallyInterface));

        foreach ($this->getCommandWorkers($command) as $worker) {
            try {
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

        //after execute
        if (!$command instanceof SubCommandInterface) {
            $result = $this->afterExecute($command, $result);
        }
        return $result;
    }

    public function internalExecute(CommandInterface $command)
    {
        if ($command instanceof WorkerInfoCommand) {
            $workerId = $command->getWorkerId();
            /** @var WorkerInterface $worker */
            $worker = $this->getWorker($workerId);
            return $worker->execute($command);
        } else {
            throw new \InvalidArgumentException(sprintf('Special operator command "%s" not supported', get_class($command)));
        }
    }

    public function __invoke(\Akademiano\Delegating\Command\CommandInterface $command)
    {
        return $this->execute($command);
    }


}

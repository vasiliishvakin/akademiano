<?php


namespace Akademiano\Operator\WorkersMap;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Operator\WorkersMap\Filter\FieldFilter;
use Akademiano\Operator\WorkersMap\Filter\Filter;

class Relation implements RelationInterface
{
    /** @var string */
    protected $workerId;

    /** @var string */
    protected $commandClass;

    /** @var int */
    protected $order = 0;

    protected $id;

    /** @var Filter[] */
    protected $filter;

    public function __construct(string $workerId, string $commandClass, int $order = 0, Filter $filter = null)
    {
        $this->workerId = $workerId;
        $this->commandClass = $commandClass;
        $this->order = $order;
        $this->filter = $filter;
    }

    /**
     * @return string
     */
    public function getWorkerId(): string
    {
        return $this->workerId;
    }

    /**
     * @return string
     */
    public function getCommandClass(): string
    {
        return $this->commandClass;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    public function getId():string
    {
        if (null === $this->id) {
            $this->id = sprintf('%s|%s', $this->getWorkerId(), $this->getCommandClass());
        }
        return $this->id;
    }

    /**
     * @return Filter
     */
    public function getFilter(): ?Filter
    {
        return $this->filter;
    }

    public function filter(CommandInterface $command)
    {
        $filter = $this->getFilter();
        if (!$filter) {
            return true;
        }
        return $filter($command);
    }

    public function __invoke(CommandInterface $command)
    {
        return $this->filter($command);
    }
}

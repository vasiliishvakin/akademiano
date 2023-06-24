<?php


namespace Akademiano\EntityOperator\Command;

class FindCommand extends EntityCommand
{
    /** @var array */
    protected $criteria = [];

    /** @var integer */
    protected $limit;

    /** @var integer */
    protected $offset;

    /** @var string|array */
    protected $orderBy;

    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function setCriteria(array $criteria): FindCommand
    {
        $this->criteria = $criteria;
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): FindCommand
    {
        $this->limit = $limit;
        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(?int $offset): FindCommand
    {
        $this->offset = $offset;
        return $this;
    }

    public function getOrderBy()
    {
        return $this->orderBy;
    }

    public function setOrderBy($orderBy = null): FindCommand
    {
        $this->orderBy = $orderBy;
        return $this;
    }
}

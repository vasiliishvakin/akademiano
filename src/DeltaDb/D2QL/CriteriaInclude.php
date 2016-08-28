<?php


namespace DeltaDb\D2QL;


trait CriteriaInclude
{
    /** @var  Criteria */
    protected $criteria;

    /**
     * @return Criteria
     */
    public function getCriteria()
    {
        if (null === $this->criteria) {
            $this->criteria = new Criteria($this->getAdapter());
        }
        return $this->criteria;
    }

    /**
     * @param Criteria $criteria
     * @return Select
     */
    public function setCriteria(Criteria $criteria)
    {
        $this->criteria = $criteria;
        return $this;
    }

    public function getCriteriaTables()
    {
        return $this->getCriteria()->getCriteriaTables();
    }

    public function getSqlWhere()
    {
        $sql = $this->getCriteria()->getSqlWhere();
        return $sql;
    }
}

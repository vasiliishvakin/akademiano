<?php


namespace DeltaDb\D2QL;


trait CriteriaRelationInclude
{
    
    protected $relation = Where::REL_AND;

    /**
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param string $relation
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;
    }
}

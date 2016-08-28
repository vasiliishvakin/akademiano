<?php


namespace DeltaDb\D2QL;


interface CriteriaIncludeInterface
{
    public function getCriteria();

    /**
     * @return array
     */
    public function getCriteriaTables();

}

<?php


namespace Akademiano\Db\Adapter\D2QL;


interface CriteriaIncludeInterface
{
    public function getCriteria();

    /**
     * @return array
     */
    public function getCriteriaTables();

}

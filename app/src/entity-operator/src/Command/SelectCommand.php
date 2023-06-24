<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Db\Adapter\D2QL\Select;

class SelectCommand extends EntityCommand
{
    /** @var Select */
    protected $select;

    /**
     * @return Select
     */
    public function getSelect(): Select
    {
        return $this->select;
    }

    public function setSelect(Select $select): SelectCommand
    {
        $this->select = $select;
        return $this;
    }
}

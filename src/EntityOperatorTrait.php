<?php


namespace EntityOperator;


trait EntityOperatorTrait
{
    /**
     * @var EntityOperator
     */
    protected $EntityOperator;

    /**
     * @return EntityOperator
     */
    public function getEntityOperator()
    {
        return $this->EntityOperator;
    }

    /**
     * @param EntityOperator $EntityOperator
     */
    public function setEntityOperator($EntityOperator)
    {
        $this->EntityOperator = $EntityOperator;
    }

}

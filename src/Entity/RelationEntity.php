<?php


namespace EntityOperator\Entity;


class RelationEntity extends Entity
{
    protected $first;
    protected $second;

    protected $firstClass;
    protected $secondClass;

    /**
     * @return mixed
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * @param mixed $first
     */
    public function setFirst($first)
    {
        $this->first = $first;
    }

    /**
     * @return mixed
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * @param mixed $second
     */
    public function setSecond($second)
    {
        $this->second = $second;
    }

    /**
     * @return mixed
     */
    public function getFirstClass()
    {
        return $this->firstClass;
    }

    /**
     * @param mixed $firstClass
     */
    public function setFirstClass($firstClass)
    {
        $this->firstClass = $firstClass;
    }

    /**
     * @return mixed
     */
    public function getSecondClass()
    {
        return $this->secondClass;
    }

    /**
     * @param mixed $secondClass
     */
    public function setSecondClass($secondClass)
    {
        $this->secondClass = $secondClass;
    }
}

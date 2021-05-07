<?php


namespace Akademiano\Db\Adapter\WhereParams;


class Comparison
{
    protected string $operator;
    protected $value;

    /**
     * Comparison constructor.
     * @param string $operator
     * @param $value
     */
    public function __construct(string $operator, $value)
    {
        $this->setOperator($operator);
        $this->setValue($value);
    }


    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     */
    protected function setOperator(string $operator): void
    {
        $this->operator = $operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    protected function setValue($value): void
    {
        $this->value = $value;
    }






}

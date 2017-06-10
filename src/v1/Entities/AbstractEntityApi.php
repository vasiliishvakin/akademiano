<?php


namespace Akademiano\Api\v1\Entities;


use Akademiano\Api\v1\AbstractApi;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\UUID\UuidComplexShortTables;

abstract class AbstractEntityApi extends AbstractApi implements EntityApiInterface
{
    /** @var  EntityOperator */
    protected $operator;

    /**
     * AbstractEntityApi constructor.
     * @param EntityOperator $operator
     */
    public function __construct(EntityOperator $operator)
    {
        $this->setOperator($operator);
    }

    /**
     * @return EntityOperator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param EntityOperator $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    public function intToUuidCST($value)
    {
        return $this->getOperator()->create(UuidComplexShortTables::class, ["value" => $value]);
    }
}

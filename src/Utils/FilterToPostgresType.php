<?php

namespace Akademiano\EntityOperator\Utils;

use Akademiano\Db\Adapter\PgsqlAdapter;
use Akademiano\Entity\EntityInterface;
use Akademiano\Utils\Object\Prototype\IntegerableInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;
use Akademiano\Utils\Parts\ResourceBuilderTrait;
use Carbon\CarbonInterval;

class FilterToPostgresType
{
    const RESOURCE_ID = PgsqlAdapter::FILTER_VALUE_RESOURCE_ID;

    use ResourceBuilderTrait;

    public function filterFieldToPostgresType($value)
    {
        if ($value instanceof EntityInterface) {
            $id = $value->getId();
            if ($id instanceof IntegerableInterface) {
                return $id->getValue();
            } elseif (is_numeric($id)) {
                return (int)$id;
            } elseif (is_scalar($id)) {
                return $id;
            } else {
                return null;
            }
        } elseif ($value instanceof \DateTime || $value instanceof \DateTimeImmutable) {
            return $value->format("Y-m-d H:i:s");
        } elseif ($value instanceof CarbonInterval) {
            return $value->spec();
        } elseif (is_bool($value)) {
            return $value ? 't' : 'f';
        } elseif ($value instanceof IntegerableInterface) {
            return $value->getInt();
        } elseif ($value instanceof StringableInterface) {
            return $value->__toString();
        } else {
            return $value;
        }
    }

    public function __invoke($value)
    {
        return $this->filterFieldToPostgresType($value);
    }
}

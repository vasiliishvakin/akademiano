<?php


namespace Akademiano\EntityOperator\Worker;

use Akademiano\Entity\Exception\BadRelatedClassException;
use Akademiano\Entity\RelationsBetweenClassesTrait;
use Akademiano\EntityOperator\Exception\BadRelatedFieldException;

trait RelationsBetweenTrait
{
    use RelationsBetweenClassesTrait;

    abstract public function getFirstField();

    abstract  public function getSecondField();


    public function getFieldName($entity)
    {
        $class = (is_object($entity)) ? $class = get_class($entity) : $entity;
        $firstClass = $this->getFirstClass();
        $secondClass = $this->getSecondClass();
        if ($class === $firstClass || is_subclass_of($class, $firstClass)) {
            return $this->getFirstField();
        } elseif ($class === $secondClass || is_subclass_of($class, $secondClass)) {
            return $this->getSecondField();
        } else {
            throw new BadRelatedClassException(sprintf('In class "%s" a "%s" is bad related class',
                    get_class($this), $class)
            );
        }
    }

    public function getAnotherField($field)
    {
        switch ($field) {
            case $this->getFirstField() :
                return $this->getSecondField();
            case $this->getSecondField():
                return $this->getFirstField();
            default:
                throw new BadRelatedFieldException(sprintf('In class "%s" a "%s" is bad related field',
                        get_class($this), $field)
                );
        }
    }

}

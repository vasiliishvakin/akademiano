<?php


namespace Akademiano\Entity;


use Akademiano\Entity\Exception\BadRelatedClassException;

trait RelationsBetweenClassesTrait
{
    abstract public function getFirstClass();

    abstract public function getSecondClass();

    public function getAnotherClass($entity)
    {
        $class = (is_object($entity)) ? $class = get_class($entity) : $entity;
        $firstClass = $this->getFirstClass();
        $secondClass = $this->getSecondClass();
        if ($class === $firstClass || is_subclass_of($class, $firstClass)) {
            return $this->getSecondClass();
        } elseif ($class === $secondClass || is_subclass_of($class, $secondClass)) {
            return $this->getFirstClass();
        } else {
            throw new BadRelatedClassException(sprintf('In class "%s" params "%s" is bad related class',
                    get_class($this), $class)
            );
        }
    }
}

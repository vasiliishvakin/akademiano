<?php


namespace Akademiano\Entity;

use Akademiano\Entity\Exception\Relations\BadRelatedClassException;

class RelationEntity extends Entity
{
    const FIRST_CLASS = Entity::class;
    const SECOND_CLASS = Entity::class;

    const FIRST_FIELD = 'first';
    const SECOND_FIELD = 'second';

    protected $first;
    protected $second;

    use RelationsBetweenTrait;

    /**
     * @return mixed
     */
    public function getFirstClass(): string
    {
        return static::FIRST_CLASS;
    }

    /**
     * @return mixed
     */
    public function getSecondClass(): string
    {
        return static::SECOND_CLASS;
    }

    public function getFirstField(): string
    {
        return self::FIRST_FIELD;
    }

    public function getSecondField(): string
    {
        return self::SECOND_FIELD;
    }

    public function getFirst(): ?EntityInterface
    {
        return $this->first;
    }

    public function setFirst($first)
    {
        $this->first = $first;
    }

    public function getSecond(): ?EntityInterface
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

    public function getAnother(EntityInterface $entity): ?EntityInterface
    {
        if ($this->getFirstClass() === $this->getSecondClass()) {
            throw new \LogicException(sprintf(
                'Could not use method "%s" with the same first and second class "%s"',
                __METHOD__, $this->getFirstClass()
            ));
        }
        $class = get_class($entity);
        switch ($class) {
            case $this->getFirstClass():
                return $this->getSecond();
                break;
            case $this->getSecondClass():
                return $this->getFirst();
                break;
            default:
                throw new BadRelatedClassException(
                    sprintf('Bad class "%s".', $class)
                );
        }
    }
}

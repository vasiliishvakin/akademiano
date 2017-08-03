<?php


namespace Akademiano\EntityOperator\Entity;

use Akademiano\Entity\Entity;
use Akademiano\Entity\EntityInterface;
use Akademiano\Entity\Exception\BadRelatedClassException;
use Akademiano\Entity\RelationsBetweenClassesTrait;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;
use Akademiano\UserEO\Model\Utils\OwneredTrait;

class RelationEntity extends Entity implements DelegatingInterface
{
    const FIRST_CLASS = Entity::class;
    const SECOND_CLASS = Entity::class;

    use DelegatingTrait;
    use RelationsBetweenClassesTrait;

    protected $first;
    protected $second;

    use OwneredTrait;

    public function getFirst()
    {
        if (null !== $this->first) {
            $class = $this->getFirstClass();
            /** @var EntityOperator $operator */
            $operator = $this->getOperator();
            $first = $operator->get($this->getFirstClass(), $this->first);
            if (!$first) {
                throw new NotFoundException(
                    sprintf('Entity with id "%s" of "%s" class not found.', dechex($this->first), $class)
                );
            }
            $this->first = $first;
        }
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
        if (null !== $this->second) {
            $class = $this->getSecondClass();
            /** @var EntityOperator $operator */
            $operator = $this->getOperator();
            $second = $operator->get($this->getSecondClass(), $this->second);
            if (!$second) {
                throw new NotFoundException(
                    sprintf('Entity with id "%s" of "%s" class not found.', dechex($this->second), $class)
                );
            }
            $this->second = $second;
        }
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
        return static::FIRST_CLASS;
    }
    /**
     * @return mixed
     */
    public function getSecondClass()
    {
        return static::SECOND_CLASS;
    }

    public function getAnother(EntityInterface $entity)
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

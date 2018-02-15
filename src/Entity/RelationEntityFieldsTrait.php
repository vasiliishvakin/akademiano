<?php


namespace Akademiano\EntityOperator\Entity;


use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\HttpWarp\Exception\NotFoundException;

trait RelationEntityFieldsTrait
{
    protected $first;
    protected $second;

    public function getFirst(): ?EntityInterface
    {
        if (!$this->first instanceof EntityInterface && null !== $this->first) {
            $class = $this->getFirstClass();
            $this->first = $this->delegate((new GetCommand($this->getFirstClass()))->setId($this->first));
            if (!$this->first) {
                throw new NotFoundException(
                    sprintf('Entity with id "%s" of "%s" class not found.', dechex($this->first), $class)
                );
            }
        }
        return $this->first;
    }

    public function getSecond():?EntityInterface
    {
        if (!$this->second instanceof EntityInterface && null !== $this->second) {
            $class = $this->getFirstClass();
            $this->second = $this->delegate((new GetCommand($this->getSecondClass()))->setId($this->second));
            if (!$this->second) {
                throw new NotFoundException(
                    sprintf('Entity with id "%s" of "%s" class not found.', dechex($this->second), $class)
                );
            }
        }
        return $this->second;
    }
}

<?php


namespace Akademiano\EntityOperator\Entity;


trait GetEncapsulatedEntityCollectionAttributeTrait
{
    abstract public function delegate(CommandInterface $command, bool $throwOnEmptyOperator = false);

    private function getEncapsulatedEntityAttribute(string $class, &$variable): ?EntityInterface
    {

        if (null !== $variable && !$variable instanceof EntityInterface) {
            $variable = $this->delegate((new GetCommand($class))->setId($variable));
        }
        return $variable;

    }
    public function getComments()
    {
        if (!$this->comments instanceof Collection) {
            if (is_array($this->comments)) {
                $criteria["id"] = $this->comments;
            }
            $criteria["entity"] = (string)$this->getId();
            /** @var EntityOperator $operator */
            $operator = $this->getOperator();
            $this->comments = $operator->find(static::ENTITY_COMMENTS_CLASS, $criteria);
        }
        return $this->comments;
    }
}

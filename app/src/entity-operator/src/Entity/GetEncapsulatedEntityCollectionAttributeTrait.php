<?php


namespace Akademiano\EntityOperator\Entity;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\Utils\Object\Collection;

trait GetEncapsulatedEntityCollectionAttributeTrait
{
    abstract public function delegate(CommandInterface $command, bool $throwOnEmptyOperator = false);

    public function getEncapsulatedEntityCollectionAttribute(string $class, &$variable, EntityInterface $entity): Collection
    {
        if (!$variable instanceof Collection) {
            if (is_array($variable)) {
                $criteria["id"] = $variable;
            }
            $criteria["entity"] = $entity->getId()->getInt();
            $variable = $this->delegate((new FindCommand($class))->setCriteria($criteria));
        }
        return $variable;
    }
}

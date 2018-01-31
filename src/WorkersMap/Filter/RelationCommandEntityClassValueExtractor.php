<?php


namespace Akademiano\EntityOperator\WorkersMap\Filter;

use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\EntityOperator\Command\EntityCommandInterface;
use Akademiano\Operator\WorkersMap\Filter\ValueExtractor;


class RelationCommandEntityClassValueExtractor extends ValueExtractor
{
    public function extract(string $fieldName,  CommandInterface $command): \Traversable
    {
        if ($command instanceof EntityCommandInterface) {
            switch ($fieldName) {
                case EntityCommandInterface::FILTER_FIELD_ENTITY_CLASS:
                    $class = $command->getEntityClass();
                    return $this->traversableEntityClass($class);
            }
        }
    }

    public function traversableEntityClass(string $class): \Generator
    {
        do {
            yield $class;
            $class = get_parent_class($class);
        } while ($class);
    }
}

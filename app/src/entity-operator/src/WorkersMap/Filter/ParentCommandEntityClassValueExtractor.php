<?php


namespace Akademiano\EntityOperator\WorkersMap\Filter;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\EntityOperator\Command\EntityCommandInterface;
use Akademiano\Operator\Command\SubCommandInterface;

class ParentCommandEntityClassValueExtractor extends RelationCommandEntityClassValueExtractor
{
    public function extract(string $fieldName, CommandInterface $command): \Traversable
    {
        if ($command instanceof SubCommandInterface) {
            if (
                $fieldName === SubCommandInterface::PARAM_PARENT_COMMAND
                ||
                $fieldName === EntityCommandInterface::FILTER_FIELD_ENTITY_CLASS
            ) {
                $parentCommand = $command->getParentCommand();
                if ($parentCommand instanceof EntityCommandInterface) {
                    return $this->traversableEntityClass($parentCommand->getEntityClass());
                }
            }
        }
        //TODO HREN!
        return (new \ArrayObject([]))->getIterator();
    }
}

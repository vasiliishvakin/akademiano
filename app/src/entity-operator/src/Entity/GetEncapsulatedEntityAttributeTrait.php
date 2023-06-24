<?php


namespace Akademiano\EntityOperator\Entity;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\GetCommand;

trait GetEncapsulatedEntityAttributeTrait
{
    abstract public function delegate(CommandInterface $command, bool $throwOnEmptyOperator = false);

    private function getEncapsulatedEntityAttribute(string $class, &$variable): ?EntityInterface
    {

        if (null !== $variable && !$variable instanceof EntityInterface) {
            $variable = $this->delegate((new GetCommand($class))->setId($variable));
        }
        return $variable;

    }
}

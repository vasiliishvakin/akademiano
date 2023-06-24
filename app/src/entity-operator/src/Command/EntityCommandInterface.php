<?php


namespace Akademiano\EntityOperator\Command;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Entity\Entity;

interface EntityCommandInterface extends CommandInterface
{
    const FILTER_FIELD_ENTITY_CLASS = 'entityClass';

    public function getEntityClass(): string;
}

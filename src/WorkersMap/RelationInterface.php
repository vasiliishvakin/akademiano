<?php


namespace Akademiano\Operator\WorkersMap;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Operator\WorkersMap\Filter\Filter;

interface RelationInterface
{
    const PARAM_ORDER = 'order';
    const PARAM_FILTER_FIELDS = 'filterFields';

    public function getWorkerId(): string;

    public function getCommandClass(): string;

    public function getOrder(): int;

    public function getId():string ;

    public function getFilter(): ?Filter;

    public function filter(CommandInterface $command);

    public function __invoke(CommandInterface $command);
}

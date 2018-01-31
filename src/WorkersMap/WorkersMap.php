<?php


namespace Akademiano\Operator\WorkersMap;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Operator\Command\SubCommandInterface;
use Akademiano\Operator\WorkersMap\Filter\FieldFilter;
use Akademiano\Operator\WorkersMap\Filter\Filter;
use Akademiano\Operator\WorkersMap\Filter\FilterFieldInterface;
use Akademiano\Utils\ArrayTools;
use Akademiano\Utils\Object\Collection;

class WorkersMap
{
    /** @var Collection|Relation[] */
    protected $relations;

    /** @var array */
    protected $map = [];

    public function getRelations(): Collection
    {
        if (!$this->relations instanceof Collection) {
            $this->relations = new Collection($this->relations);
        }
        return $this->relations;
    }

    public function addRelation(Relation $relation)
    {
        $this->getRelations()[$relation->getId()] = $relation;
        if (isset($this->map[$relation->getCommandClass()])) {
            unset($this->map[$relation->getCommandClass()]);
        }
    }

    public function createFieldFilterFromParams(string $fieldName, array $filterParams): FilterFieldInterface
    {
        $extractor = null;
        if (isset($filterParams['extractor'])) {
            switch (true) {
                case $filterParams['extractor'] instanceof \Closure:
                    $extractor = $filterParams['extractor'];
                    break;
                case is_callable($filterParams['extractor']):
                    $extractor = \Closure::fromCallable($filterParams['extractor']);
                    break;
                case is_string($filterParams['extractor']) && class_exists($filterParams['extractor']):
                    $extractor = \Closure::fromCallable(new $filterParams['extractor']);
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('extractor is not allowed type ""%s', json_encode($filterParams['extractor'])));
            }
        }
        $fieldFilter = new FieldFilter($fieldName, $filterParams['assertion'], $extractor);
        return $fieldFilter;
    }

    public function addRelations(iterable $relationsData)
    {
        foreach ($relationsData as $workerId => $commandRelations) {
            foreach ($commandRelations as $commandClass => $relationRow) {
                if (isset($relationRow['filterFields'])) {
                    $fieldFilters = [];
                    foreach ($relationRow['filterFields'] as $fieldName => $filterParams) {
                        if (ArrayTools::getArrayType($filterParams) === ArrayTools::ARRAY_TYPE_NUM) {
                            foreach ($filterParams as $fieldFilterRow) {
                                $fieldFilter = $this->createFieldFilterFromParams($fieldName, $fieldFilterRow);
                                $fieldFilters[] = $fieldFilter;
                            }
                        } else {
                            $fieldFilter = $this->createFieldFilterFromParams($fieldName, $filterParams);
                            $fieldFilters[] = $fieldFilter;
                        }
                    }

                    if (!empty($fieldFilters)) {
                        $filter = new Filter($fieldFilters);
                    }
                }
                $relation = new Relation($workerId, $commandClass, $relationRow['order'] ?? 0, $filter ?? null);
                $this->addRelation($relation);
            }
        }
    }

    public function unsetRelations(iterable $relationsData)
    {
        $this->relations = null;
        $this->map = [];
    }

    /**
     * @param $commandClass
     * @return Collection|Relation[]
     */
    public function getCommandRelations($commandClass): Collection
    {
        if (!isset($this->map[$commandClass])) {
            $this->map[$commandClass] = $this->getRelations()->filter(
                function (Relation $item) {
                    return $item->getCommandClass();
                },
                $commandClass
            )->usort(function (Relation $itemA, Relation $itemB) {
                if ($itemA->getOrder() === $itemB->getOrder()) {
                    return 0;
                }
                return ($itemA->getOrder() < $itemB->getOrder()) ? -1 : 1;
            });
        }
        return $this->map[$commandClass];
    }

    public function getWorkersIds($neededCommandClass, CommandInterface $command): \Generator
    {
        $commandRelations = $this->getCommandRelations($neededCommandClass);
        if ($commandRelations->isEmpty()) {
            return;
        }

        $filteredRelations = [];

        start_filtering:
        $iterations = 0;
        foreach ($commandRelations as $relation) {
            $iterations++;
            if ($filterResult = $relation->filter($command)) {
                if ($filterResult <= 1) {
                    yield $relation->getWorkerId();
                    continue;
                }
                $filteredRelations[] = ['relation' => $relation, 'effort' => $filterResult];
            }
        }

        if ($iterations<$commandRelations->count()) {
            $commandRelations = $commandRelations->slice($iterations);
            goto start_filtering;
        }

        usort($filteredRelations, function ($itemA, $itemB) {
            $a = $itemA['effort'];
            $b = $itemB['effort'];
            if ($a === $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });

        foreach ($filteredRelations as $relationData) {
            /** @var Relation $relation */
            $relation = $relationData['relation'];
            yield $relation->getWorkerId();
        }
    }
}

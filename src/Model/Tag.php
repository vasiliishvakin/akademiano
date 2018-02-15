<?php

namespace Akademiano\Content\Tags\Model;


use Akademiano\Entity\NamedEntity;
use Akademiano\Entity\UuidInterface;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\UserEO\Model\Utils\OwneredTrait;
use Akademiano\Utils\Object\Collection;
use function foo\func;

class Tag extends NamedEntity
{
    use OwneredTrait;

    /** @var  Dictionary[]|Collection|array */
    protected $dictionaries;

    /**
     * @return  Dictionary[]|Collection
     */
    public function getDictionaries(): Collection
    {
        if (!$this->dictionaries instanceof Collection) {
            if (null === $this->dictionaries) {
                /** @var Collection $relations */
                $relations = $this->delegate((new FindCommand(TagDictionaryRelation::class))->setCriteria([TagsDictionariesRelationWorker::FIELD_FIRST => $this]));
                $this->dictionaries = $relations->lists(TagsDictionariesRelationWorker::FIELD_SECOND);
            } else {
                $this->dictionaries = $this->delegate((new FindCommand(Dictionary::class))->setCriteria(['id' => $this->dictionaries]));
            }
        }
        return $this->dictionaries;
    }

    public function setDictionaries(array $dictionaries): void
    {
        if (count($dictionaries) === 0) {
            $this->dictionaries = new Collection([]);
        } else {
            $this->dictionaries = $dictionaries;
        }
    }
}

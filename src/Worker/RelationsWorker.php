<?php


namespace EntityOperator\Worker;


use DeltaUtils\Object\Collection;
use EntityOperator\Command\CommandInterface;
use EntityOperator\Command\FindCommand;
use EntityOperator\Command\RelationLoadCommand;
use EntityOperator\Entity\EntityInterface;
use EntityOperator\Entity\RelationEntity;
use EntityOperator\Operator\DelegatingInterface;
use EntityOperator\Operator\DelegatingTrait;
use EntityOperator\Operator\FinderInterface;
use EntityOperator\Operator\KeeperInterface;
use EntityOperator\Worker\Exception\BadField;
use EntityOperator\Worker\Exception\BadRelatedClass;

class RelationsWorker extends PostgresWorker implements DelegatingInterface, FinderInterface
{
    const FIELD_FIRST = "first";
    const FIELD_SECOND = "second";

    use DelegatingTrait;

    protected $firstClass;

    protected $secondClass;

    protected $relationClass;

    public function __construct($firstClass, $secondClass, $relationClass, $table = "relations")
    {
        $this->setTable($table);
        $this->addFields([self::FIELD_FIRST, self::FIELD_SECOND]);
        $this->setFirstClass($firstClass);
        $this->setSecondClass($secondClass);
        $this->setRelationClass($relationClass);
    }

    /**
     * @return mixed
     */
    public function getFirstClass()
    {
        return $this->firstClass;
    }

    /**
     * @param mixed $firstClass
     */
    public function setFirstClass($firstClass)
    {
        $this->firstClass = $firstClass;
    }

    /**
     * @return mixed
     */
    public function getSecondClass()
    {
        return $this->secondClass;
    }

    /**
     * @param mixed $secondClass
     */
    public function setSecondClass($secondClass)
    {
        $this->secondClass = $secondClass;
    }

    /**
     * @return mixed
     */
    public function getRelationClass()
    {
        return $this->relationClass;
    }

    /**
     * @param mixed $relationClass
     */
    public function setRelationClass($relationClass)
    {
        $this->relationClass = $relationClass;
    }

    public function getAnotherClass($entity)
    {
        $class = (is_object($entity)) ? $class = get_class($entity) : $entity;
        switch ($class) {
            case $this->getFirstClass() :
                return $this->getSecondClass();
            case $this->getSecondClass():
                return $this->getFirstClass();
            default:
                throw new BadRelatedClass();
        }
    }

    public function getFieldName($entity)
    {
        $class = (is_object($entity)) ? $class = get_class($entity) : $entity;
        switch ($class) {
            case $this->getFirstClass() :
                return self::FIELD_FIRST;
            case $this->getSecondClass():
                return self::FIELD_SECOND;
            default:
                throw new BadRelatedClass($this, $class);
        }
    }

    public function getAnotherField($field)
    {
        switch ($field) {
            case self::FIELD_FIRST :
                return self::FIELD_SECOND;
            case self::FIELD_SECOND:
                return self::FIELD_FIRST;
            default:
                throw new BadField($this, $field);
        }
    }

    public function getLinked(EntityInterface $entity)
    {
        $knownField = $this->getFieldName($entity);
        $criteriaRelations = [$knownField => $entity->getId()];
        $command = new FindCommand($this->getRelationClass(), $criteriaRelations);
        /** @var Collection|RelationEntity[] $relations */
        $relations = $this->delegate($command);
        if ($relations->isEmpty()) {
            return new Collection();
        }

        $anotherField = $this->getAnotherField($knownField);
        $secondIds = $relations->lists($anotherField);
        $anotherClass = $this->getAnotherClass($entity);

        $command = new FindCommand($anotherClass, ["id" => $secondIds]);
        $entities = $this->delegate($command);
        return $entities;
    }

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case RelationLoadCommand::COMMAND_RELATION_LOAD:
                return $this->getLinked($command->getParams("entity"));
                break;
            default:
                return parent::execute($command);

        }
    }
}

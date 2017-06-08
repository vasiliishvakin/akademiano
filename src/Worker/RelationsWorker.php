<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Db\Adapter\D2QL\Criteria;
use Akademiano\Db\Adapter\D2QL\Join;
use Akademiano\Db\Adapter\D2QL\Prototype\WherePrototype;
use Akademiano\Db\Adapter\D2QL\Where;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\DeleteCommand;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\EntityOperator\Command\InfoWorkerCommand;
use Akademiano\EntityOperator\Command\RelationLoadCommand;
use Akademiano\EntityOperator\Command\RelationParamsCommand;
use Akademiano\EntityOperator\Entity\RelationEntity;
use Akademiano\EntityOperator\Worker\Exception\BadFieldException;
use Akademiano\EntityOperator\Worker\Exception\BadRelatedClassException;
use Akademiano\Operator\Command\CommandInterface;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;
use Akademiano\Utils\Object\Collection;


class RelationsWorker extends PostgresWorker implements DelegatingInterface, FinderInterface
{
    const FIELD_FIRST = "first";
    const FIELD_SECOND = "second";

    use WorkerMetaMapPropertiesTrait;
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
        $firstClass = $this->getFirstClass();
        $secondClass = $this->getSecondClass();
        if ($class === $firstClass || is_subclass_of($class, $firstClass)) {
            return $this->getSecondClass();
        } elseif ($class === $secondClass || is_subclass_of($class, $secondClass)) {
            return $this->getFirstClass();
        } else {
            throw new BadRelatedClassException($this, $class);
        }
    }

    public function getFieldName($entity)
    {
        $class = (is_object($entity)) ? $class = get_class($entity) : $entity;
        $firstClass = $this->getFirstClass();
        $secondClass = $this->getSecondClass();
        if ($class === $firstClass || is_subclass_of($class, $firstClass)) {
            return self::FIELD_FIRST;
        } elseif ($class === $secondClass || is_subclass_of($class, $secondClass)) {
            return self::FIELD_SECOND;
        } else {
            throw new BadRelatedClassException($this, $class);
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
                throw new BadFieldException($this, $field);
        }
    }

    public function findByEntity(EntityInterface $entity)
    {
        $knownField = $this->getFieldName($entity);
        $criteriaRelations = [$knownField => $entity->getId()];
        $command = new FindCommand($this->getRelationClass(), $criteriaRelations);
        /** @var Collection|RelationEntity[] $relations */
        $relations = $this->delegate($command);
        if ($relations->isEmpty()) {
            return new Collection();
        }
        return $relations;
    }

    public function getLinked(EntityInterface $entity)
    {
        $relations = $this->findByEntity($entity);

        $knownField = $this->getFieldName($entity);
        $anotherField = $this->getAnotherField($knownField);
        $secondIds = $relations->lists($anotherField);

        if (empty($secondIds)) {
            return new Collection();
        }
        $anotherClass = $this->getAnotherClass($entity);

        $command = new FindCommand($anotherClass, ["id" => $secondIds]);
        $entities = $this->delegate($command);
        return $entities;
    }

    public function getAnother(RelationEntity $relation, EntityInterface $entity)
    {
        $knownField = $this->getFieldName($entity);
        $anotherField = $this->getAnotherField($knownField);
        $methodGetAnother = "get" . ucfirst($anotherField);
        $secondId = $relation->$methodGetAnother();
        $anotherClass = $this->getAnotherClass($entity);
        $command = new GetCommand($secondId, $anotherClass);
        $entityAnother = $this->delegate($command);
        return $entityAnother;
    }

    public function getParam($paramName, $params = [])
    {
        switch ($paramName) {
            case self::FIELD_FIRST . "Class":
                return $this->getFirstClass();
            case self::FIELD_SECOND . "Class" :
                return $this->getSecondClass();
            case "anotherClass" :
                if (!isset($params["class"])) {
                    throw new \InvalidArgumentException("not set current class in params command");
                }
                return $this->getAnotherClass($params["class"]);
        }
    }

    public function deleteAnother(EntityInterface $entity)
    {
        $anotherEntities = $this->getLinked($entity);
        foreach ($anotherEntities as $anotherEntity) {
            $command = new DeleteCommand($anotherEntity);
            $this->delegate($command);
        }
    }

    protected function getTableForClass($class)
    {
        $command = new InfoWorkerCommand("table", $class);
        $table = $this->delegate($command);
        return $table;
    }


    public function getRelatedCriteria($knownClass = null, $joinType = Join::TYPE_INNER, $relatedCondition = null)
    {
        if (null === $knownClass) {
            $knownClass = $this->getFirstClass();
        }
        $criteria = new Criteria($this->getAdapter());
        $knownField = $this->getFieldName($knownClass);
        $destinationField = $this->getAnotherField($knownField);
        $destinationClass = $this->getAnotherClass($knownClass);

        $relationTable = $this->getTable();
        $knownTable = $this->getTableForClass($knownClass);
        $destinationTable = $this->getTableForClass($destinationClass);

        //join for relation table
        $criteria->createJoin($relationTable, $knownField, $knownTable, "id", $joinType);

        //join for another table
        $criteria->createJoin($destinationTable, "id", $relationTable, $destinationField, $joinType);

        //create where for joined table
        if (null !== $relatedCondition) {
            if (!$relatedCondition instanceof WherePrototype) {
                $relatedCondition = new WherePrototype($relatedCondition);
            }
            $criteria->createWhere($relatedCondition->getField(), $relatedCondition->getValue(),
                $relatedCondition->getOperator(), $destinationTable, Where::REL_AND, $relatedCondition->getType()
            );
        }
        return $criteria;
    }

    public function getAttribute($attribute, array $params = [])
    {
        switch ($attribute) {
            case "relatedCriteria" : {
                $currentClass = $params["currentClass"];
                $type = isset($params["joinType"]) ? $params["joinType"] : Join::TYPE_INNER;
                $relatedCondition = isset($params["relatedCondition"]) ? $params["relatedCondition"] : null;
                return $this->getRelatedCriteria($currentClass, $type, $relatedCondition);
                break;
            }
            case "relatedTable" : {
                $currentClass = $params["currentClass"];
                $relatedClass = $this->getAnotherClass($currentClass);
                return $this->getTableForClass($relatedClass);
                break;
            }
            default:
                return parent::getAttribute($attribute, $params);
        }
    }

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case RelationLoadCommand::COMMAND_NAME:
                return $this->getLinked($command->getParams("entity"));
                break;
            case RelationParamsCommand::COMMAND_NAME :
                return $this->getParam($command->getParams("param"), $command->getParams());
                break;
            case FindCommand::COMMAND_NAME :
                if ($command->hasParam("entity")) {
                    return $this->findByEntity($command->getParams("entity"));
                } else {
                    return parent::execute($command);
                }
                break;
            case DeleteCommand::COMMAND_NAME :
                $id = $command->getParams("id");
                $relation = $command->getParams("entity");
                if ($command->hasParam("currentLinkedEntity")) {
                    $entity = $command->getParams("currentLinkedEntity");
                    $anotherEntity = $this->getAnother($relation, $entity);
                }
                parent::delete($id);
                if (isset($anotherEntity)) {
                    $commandDeleteAnother = new DeleteCommand($anotherEntity);
                    $this->delegate($commandDeleteAnother);
                }
                break;
            default:
                return parent::execute($command);

        }
    }
}

<?php


namespace DeltaPhp\Operator\Worker;


use DeltaPhp\Operator\Command\DeleteCommand;
use DeltaPhp\Operator\Command\GetCommand;
use DeltaUtils\Object\Collection;
use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Command\FindCommand;
use DeltaPhp\Operator\Command\RelationLoadCommand;
use DeltaPhp\Operator\Command\RelationParamsCommand;
use DeltaPhp\Operator\Entity\EntityInterface;
use DeltaPhp\Operator\Entity\RelationEntity;
use DeltaPhp\Operator\DelegatingInterface;
use DeltaPhp\Operator\DelegatingTrait;
use DeltaPhp\Operator\Worker\Exception\BadField;
use DeltaPhp\Operator\Worker\Exception\BadRelatedClass;

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

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case RelationLoadCommand::COMMAND_RELATION_LOAD:
                return $this->getLinked($command->getParams("entity"));
                break;
            case RelationParamsCommand::COMMAND_RELATION_PARAMS :
                return $this->getParam($command->getParams("param"), $command->getParams());
                break;
            case CommandInterface::COMMAND_FIND :
                if ($command->hasParam("entity")) {
                    return $this->findByEntity($command->getParams("entity"));
                } else {
                    return parent::execute($command);
                }
                break;
            case CommandInterface::COMMAND_DELETE :
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

<?php


namespace Articles\Model;


use DeltaDb\D2QL\Criteria;
use DeltaDb\D2QL\Select;
use DeltaPhp\Operator\Command\AfterCommandInterface;
use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Command\CreateCommand;
use DeltaPhp\Operator\Command\CreateSelectCommand;
use DeltaPhp\Operator\Command\SelectCommand;
use DeltaPhp\Operator\DelegatingInterface;
use DeltaPhp\Operator\DelegatingTrait;
use DeltaPhp\Operator\EntityOperator;
use DeltaPhp\Operator\Worker\PostgresWorker;
use DeltaPhp\Operator\Entity\EntityInterface;
use DeltaPhp\Operator\Command\DeleteCommand;
use DeltaPhp\Operator\Command\FindCommand;
use DeltaPhp\Operator\Command\PreCommandInterface;
use DeltaUtils\Object\Collection;

class ArticleWorker extends PostgresWorker implements DelegatingInterface
{
    use DelegatingTrait;

    public function __construct()
    {
        $this->setTable("articles");
        $this->addFields(["title", "description", "content"]);
    }

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case PreCommandInterface::PREFIX_COMMAND_PRE . CommandInterface::COMMAND_DELETE:
                $entity = $command->getParams("entity");
                $this->deleteImages($entity);
                $this->clearTags($entity);
                break;
            case AfterCommandInterface::PREFIX_COMMAND_AFTER . CommandInterface::COMMAND_SAVE:
                $entity = $command->getParams("entity");
                $tags = $entity->getTags();
                $this->saveTags($entity, $tags);
                break;
            case GetDatesCommand::COMMAND_GET_DATES:
                $criteria = $command->getParams("criteria");
                return $this->getDates($criteria);
                break;
            default:
                return parent::execute($command);
        }
    }

    public function deleteImages(EntityInterface $entity)
    {
        //удалить файлы
        $command = new FindCommand(ArticleImageRelation::class, null, null, null, null, ["entity" => $entity]);
        $relations = $this->delegate($command);

        foreach ($relations as $relation) {
            $command = new DeleteCommand($relation, ["currentLinkedEntity" => $entity]);
            $this->delegate($command);
        }
    }

    public function clearTags(EntityInterface $entity)
    {
        $command = new FindCommand(ArticleTagRelation::class, null, null, null, null, ["entity" => $entity]);
        $relations = $this->delegate($command);

        foreach ($relations as $relation) {
            $command = new DeleteCommand($relation);
            $this->delegate($command);
        }

    }

    public function saveTags(EntityInterface $entity, $tags)
    {
        $this->clearTags($entity);
        if (empty($tags) || $tags->isEmpty()) {
            return;
        }
        /** @var EntityOperator $operator */
        $operator = $this->getOperator();

        foreach ($tags as $tag) {
            if (is_scalar($tag)) {
                $tag = $operator->find(Tag::class, $tag);
            }
            /** @var ArticleTagRelation $relation */
            $relation = $operator->create(ArticleTagRelation::class);
            $relation->setFirst($entity);
            $relation->setSecond($tag);
            $operator->save($relation);
        }
        return;
    }

    public function getDates(Criteria $criteria = null)
    {
        /** @var Select $select */
        $select = $this->getOperator()->execute(new CreateSelectCommand(Article::class));
        $select->setDistinct(true);
        $select->addField("to_char(__TABLE__.created, 'YYYY-MM-DD')", null, true);
        if ($criteria) {
            $select->addCriteria($criteria);
        }
        $command = new SelectCommand(Article::class, $select);
        $result = (array) $this->getOperator()->execute($command);
        return $result;
    }
}

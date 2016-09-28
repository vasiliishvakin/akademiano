<?php


namespace Articles\Model;


use DeltaCore\Config;
use DeltaCore\Parts\Configurable;
use DeltaPhp\Operator\EntityOperator;
use DeltaPhp\Operator\IncludeOperatorTrait;
use DeltaPhp\Operator\Command\InfoWorkerCommand;
use DeltaPhp\TagsDictionary\Entity\Tag;
use DeltaPhp\TagsDictionary\Entity\DictionaryTagRelation;

class ArticleTags
{
    use Configurable;
    use IncludeOperatorTrait;


    public function __construct(EntityOperator $operator, Config $config)
    {
        $this->setOperator($operator);
        $this->setConfig($config);
    }

    public function getTags()
    {
        $dictionary = $this->getConfig()->getOrThrow(["Tags", "Dictionaries"])->toArray();
        $dictionary = hexdec(reset($dictionary));

        $operator = $this->getOperator();

        $dictionaryCriteria = $operator->execute(
            new InfoWorkerCommand("relatedCriteria",
                DictionaryTagRelation::class,
                [
                    "currentClass" => Tag::class,
                    "relatedCondition" => $dictionary,
                ]
            )
        );
        $tags = $operator->find(Tag::class, $dictionaryCriteria);
        return $tags;
    }
}

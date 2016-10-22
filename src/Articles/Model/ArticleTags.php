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

    protected $dictionaries;


    public function __construct(EntityOperator $operator, Config $config)
    {
        $this->setOperator($operator);
        $this->setConfig($config);
    }

    public function getDictionaries()
    {
        if (null === $this->dictionaries) {
            $dictionaries = $this->getConfig()->getOrThrow(["Tags", "Dictionaries"])->toArray();
            $dictionaries = array_map(function ($val) {
                return hexdec($val);
            }, $dictionaries);
            $this->dictionaries = $dictionaries;
        }
        return $this->dictionaries;
    }

    public function getDictionary()
    {
        $dictionaries = $this->getDictionaries();
        return reset($dictionaries);
    }

    public function getTags()
    {
        $dictionary = $this->getDictionary();

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

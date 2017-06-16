<?php


namespace Akademiano\Api\v1\Entities;


use Akademiano\Api\v1\AbstractApi;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\UUID\UuidComplexShortTables;
use PhpOption\Some;
use PhpOption\None;

abstract class AbstractEntityApi extends AbstractApi implements EntityApiInterface
{
    /** @var  EntityOperator */
    protected $operator;

    /**
     * AbstractEntityApi constructor.
     * @param EntityOperator $operator
     */
    public function __construct(EntityOperator $operator = null)
    {
        if (null !== $operator) {
            $this->setOperator($operator);
        }
    }

    /**
     * @return EntityOperator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param EntityOperator $operator
     */
    public function setOperator(EntityOperator $operator)
    {
        $this->operator = $operator;
    }

    public function intToUuidCST($value)
    {
        return $this->getOperator()->create(UuidComplexShortTables::class, ["value" => $value]);
    }

    /**
     * @param $id
     * @return EntityInterface
     */
    abstract protected function getRaw($id);

    /**
     * @param $id
     * @return \PhpOption\Option
     */
    public function get($id)
    {
        $item = $this->getRaw($id);
        if ($item) {
            return new Some($item);
        } else {
            return None::create();
        }
    }
}

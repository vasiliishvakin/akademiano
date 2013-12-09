<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Repository;

use DeltaDb\Table;

/**
 * Class AbstractRepository
 * @package DeltaDb\Repository
 * @method  __construct(array $params) Params: ['class', 'table', 'dbaName' => null]
 */
abstract class AbstractRepository extends Table
{
    protected $class;
    protected $dbFields;

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

} 
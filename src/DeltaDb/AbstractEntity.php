<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb;


abstract class AbstractEntity implements EntityInterface
{

    abstract public function getId();

    /**
     * @return array
     */
    public function getFieldsList()
    {
        $methods = get_class_methods($this);
        $fields = [];
        foreach($methods as $method) {
            if ($pos = strpos($method, "get") !== false) {
                $field = substr($method, $pos);
                $fields[] = $field;
            }
        }
        return $fields;
    }


} 
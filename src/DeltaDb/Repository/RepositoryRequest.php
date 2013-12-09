<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Repository;

use OrbisTools\Parts\GetRequest;

class RepositoryRequest extends AbstractRepositoryAdditional
{
    use GetRequest;

    public function getFields()
    {
        $request = $this->getRequest();
        $dbFields = $this->getRepository()->getDbFields();
        $dbFields[] = 'id';
        $newFields = $request->getParams($dbFields);
        $newFields = array_filter($newFields, function ($var) {return !is_null($var);});
        return $newFields;
    }

} 
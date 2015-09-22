<?php

namespace DeltaDb;


use DeltaCore\Parts\MagicSetGetManagers;
use DeltaCore\Prototype\MagicMethodInterface;
use DeltaDb\Adapter\AdapterInterface;
use DeltaUtils\ArrayUtils;
use DeltaUtils\Object\Collection;
use DeltaUtils\Parts\InnerCache;
use DeltaUtils\StringUtils;
use Psr\Log\InvalidArgumentException;
use DeltaDb\EntityInterface;

class Repository implements RepositoryInterface
{
    use InnerCache;
    use MagicSetGetManagers;

    const METHOD_SET = 'set';
    const METHOD_GET = 'get';
    const FILTER_IN = 'input';
    const FILTER_OUT = 'output';

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var IdentityMap[]
     */
    protected $idMaps = [];

    protected $referredIds = [];

    protected $dba = DbaStorage::DBA_DEFAULT;

    protected $metaInfo = [
        'table' => null,
        'class' => 'Entity',
        'id' => 'id_field',
        'fields' => [
            'field_name' => [
                'set' => 'setMethodInEntity',
                'get' => 'getMethodInEntity',
                'filters' => [
                    'input' => 'filterFromDbToEntity',
                    'output' => 'filterFromEntityDb',
                ],
                'validators' => [

                ]
            ]
        ]
    ];

    protected $someChanged = false;
    protected $lastChanged;

    /**
     * @return boolean
     */
    public function isSomeChanged()
    {
        return $this->someChanged;
    }

    public function setChanged()
    {
        $this->lastChanged = null;
        $this->someChanged = true;
    }

    /**
     * @param boolean $isSomeChanged
     */
    public function setIsSomeChanged($isSomeChanged)
    {
        $this->isSomeChanged = $isSomeChanged;
    }

    protected function checkMethodCallable($object, $method)
    {
        if (null === $method) {
            return false;
        }
        $class = get_class($object);
        $id = "chmtcl" . $class . $method;
        if (!$result = $this->getInnerCache($id)) {
            $result = ($object instanceof MagicMethodInterface) ?: (method_exists($object, $method) && is_callable([$object, $method]));
            $this->setInnerCache($id, $result);
        }
        return $result;
    }

    public function setTable($table)
    {
        $this->metaInfo["table"] = $table;
    }

    public function getTable()
    {
        $table =  $this->getMetaInfo("table");
        if (empty($table)) {
            $class = StringUtils::cutClassName($this);
            $class = lcfirst(substr($class, 0, strpos($class, "Manager")));
            $table = StringUtils::camelCaseToLowDash($class);
            $table = explode("_",  $table);
            $table[count($table) -1] = StringUtils::pluralEn($table[count($table) -1]);
            $table = implode("_", $table);
            $this->setTable($table);
        }
        return $table;
    }

    public function getEntityClass()
    {
        $className = $this->getMetaInfo("class");
        if (empty($className)) {
            $namespace = StringUtils::cutNamespace($this);
            $class = StringUtils::cutClassName($this);
            $class = StringUtils::singularEn(substr($class, 0, strpos($class, "Manager")));
            $className = $namespace . "\\" . ucfirst($class);
            $this->metaInfo["class"] = $className;
        }
        return $className;
    }

    /**
     * @param mixed $dba
     */
    public function setDba($dba)
    {
        $this->dba = $dba;
    }

    /**
     * @return AdapterInterface
     */
    public function getDba()
    {
        return $this->dba;
    }

    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter()
    {
        if (is_null($this->adapter)) {
            $this->adapter = DbaStorage::getDba($this->getDba());
        }
        return $this->adapter;
    }

    public function setIdMap(IdentityMap $idMap, $entityClass = null)
    {
        $entityClass = $entityClass ?: $this->getEntityClass();
        $this->idMaps[$entityClass] = $idMap;
    }

    public function getIdMap($entityClass = null)
    {
        $entityClass = $entityClass ? : $this->getEntityClass();
        if (empty($this->idMaps[$entityClass])) {
            $this->idMaps[$entityClass] = new IdentityMap();
        }
        return $this->idMaps[$entityClass];
    }

    public function addReferredIds(array $ids)
    {
        $ids = array_diff($ids, $this->referredIds);
        $this->referredIds = array_merge($this->referredIds, $ids);
    }

    public function clearReferredIds()
    {
        $this->referredIds = [];
    }

    /**
     * @return array
     */
    public function getReferredIds()
    {
        return $this->referredIds;
    }

    /**
     * @param array|null $path
     * @param null|mixed $default
     * @return array|mixed|null
     */
    public function getMetaInfo($path = null, $default = null)
    {
        return ArrayUtils::getByPath($this->metaInfo, $path, $default);
    }

    public function getIdField()
    {
        return $this->getMetaInfo("id", "id");
    }

    public function getExternalFieldsList()
    {
        return $this->getMetaInfo("externalFields", []);
    }

    public function getFieldsList()
    {
        $cacheId = "fieldList";
        if ($fields = $this->getInnerCache($cacheId)) {
            return $fields;
        }
        $fieldsData = $this->getMetaInfo("fields");
        $fieldsDataType = ArrayUtils::getArrayType($fieldsData);
        switch($fieldsDataType) {
            case 1 :
                $fields = array_keys($fieldsData);
                break;
            case -1:
                $fields = array_values($fieldsData);
                break;
            case 0:
                $fields = [];
                foreach($fieldsData as $key=>$value) {
                    $fields[] =  (is_string($key)) ? $key : $value;
                }
                break;
        }
        $this->setInnerCache($cacheId, $fields);
        return $fields;
    }

    public function getFieldMeta($field)
    {
        $cacheId = "fieldMeta|$field";
        if ($this->hasInnerCache($cacheId)) {
            return $this->getInnerCache($cacheId);
        }
        $fieldMeta = $this->getMetaInfo(['fields', $field]);
        if (!$fieldMeta) {
            $fieldMeta = $this->getMetaInfo(["externalFields", $field]);
        }
        $this->setInnerCache($cacheId, $fieldMeta);
        return $fieldMeta;
    }

    public function getFieldMethod($field, $method)
    {
        $fieldMeta = $this->getFieldMeta($field);
        if ((!is_array($fieldMeta) || empty($fieldMeta)) || (!isset($fieldMeta["set"]) && !isset($fieldMeta["get"]))) {
            $field = StringUtils::lowDashToCamelCase($field);
            return $method . ucfirst($field);
        }
        if (!isset($fieldMeta[$method])) {
            return null;
        }
        $fieldMethod = $fieldMeta[$method];
        return $fieldMethod;
    }

    public function getFieldFilter($field, $filter)
    {
        $fieldMeta = $this->getFieldMeta($field);
        if (!$fieldMeta) {
            return null;
        }
        return ArrayUtils::getByPath($fieldMeta, ["filters", $filter]);
    }

    public function getFieldValidators($field)
    {
        return $this->getMetaInfo(['fields', $field, 'validators'], []);
    }

    public function validateField($entity, $field, $value)
    {
        $validators = $this->getFieldValidators($field);
        foreach($validators as $validator) {
            if (method_exists($entity, $validator)) {
                $result = $entity->{$validator}($value);
                if ($result === false) {
                    return false;
                }
            }
        }
        return true;
    }

    public function setField(EntityInterface $entity, $field, $value)
    {
        $setMethod = $this->getFieldMethod($field, self::METHOD_SET);
        $inputFilter = $this->getFieldFilter($field, self::FILTER_IN);
        if ($this->checkMethodCallable($this, $inputFilter)) {
            $value = $this->{$inputFilter}($value);
        }

        if ($this->checkMethodCallable($entity, $setMethod)) {
            return $entity->{$setMethod}($value);
        }
        return false;
    }

    public function getField(EntityInterface $entity, $field)
    {
        $getMethod = $this->getFieldMethod($field, self::METHOD_GET);
        $outputFilter = $this->getFieldFilter($field, self::FILTER_OUT);
        if (!$this->checkMethodCallable($entity, $getMethod)) {
            return null;
        }
        $value =  $entity->{$getMethod}();
        if ($this->checkMethodCallable($this, $outputFilter)) {
            $value = $this->{$outputFilter}($value);
        }
        return $value;
    }

    public function findRaw(array $criteria = [], $table = null, $limit = null, $offset = null, $orderBy = null)
    {
        $adapter = $this->getAdapter();
        if (is_null($table)) {
            $table = $this->getTable();
        }
        $data = $adapter->selectBy($table, $criteria, $limit, $offset, $orderBy);
        return $data;
    }

    public function saveRaw(array $fields, $table = null, $rawFields = null)
    {
        if (is_null($table)) {
            $table = $this->getTable();
        }
        $idName = $this->getIdField($table);
        if (isset($fields[$idName]) && !empty($fields[$idName])) {
            return $this->updateRaw($fields, $table, $rawFields);
        } else {
            $result = $this->insertRaw($fields, $table, $rawFields);
            if (empty($result)) {
                return false;
            }
            return $result;
        }
    }

    public function insertRaw(array $fields, $table = null, $rawFields = null)
    {
        $adapter = $this->getAdapter();
        if (is_null($table)) {
            $table = $this->getTable();
        }
        $idField = $this->getIdField($table);
        if (isset($fields[$idField]) || array_key_exists($idField, $fields)) {
            unset($fields[$idField]);
        }
        $fields = ArrayUtils::filterNulls($fields);
        if (!empty($rawFields)) {
            $rawFields = ArrayUtils::filterNulls($rawFields);
        }
        return $adapter->insert($table, $fields, $idField, $rawFields);
    }

    public function updateRaw($fields, $table = null, $rawFields = null)
    {
        $adapter = $this->getAdapter();
        if (is_null($table)) {
            $table = $this->getTable();
        }
        $idField = $this->getIdField($table);
        $id = $fields[$idField];
        unset($fields[$idField]);
        return $adapter->update($table, $fields, [$idField => $id], $rawFields);
    }

    public function deleteRaw(array $criteria = [], $table = null)
    {
        $adapter = $this->getAdapter();
        if (is_null($table)) {
            $table = $this->getTable();
        }
        return $adapter->delete($table, $criteria);
    }

    public function deleteById($id, $table = null)
    {
        if (is_null($table)) {
            $table = $this->getTable();
        }
        $idField = $this->getIdField($table);
        return $this->deleteBy([$idField => $id], $table);
    }

    public function create(array $data = null)
    {
        $entityClass = $this->getEntityClass();
        /** @var EntityInterface $entity */
        $entity = new $entityClass;
        if (!is_null($data)) {
            $this->load($entity, $data);
        }

        return $entity;
    }

    public function save(EntityInterface $entity)
    {
        $this->setChanged();
        $data = $this->reserve($entity);
        $fields = isset($data["fields"]) ? $data["fields"] : $data;
        $rawFields = isset($data["rawFields"]) ? $data["rawFields"] : null;
        $table = $this->getTable($entity);
        $idField = $this->getIdField($table);
        if (isset($fields[$idField]) && !empty($fields[$idField])) {
            return $this->updateRaw($fields, $table, $rawFields);
        } else {
            $result = $this->insertRaw($fields, $table, $rawFields);
            if (!$result) {
                return false;
            }
            $this->setField($entity, $idField, $result);
            $entityClass = $this->getEntityClass();
            $this->getIdMap($entityClass)->set($result, $entity);
            return true;
        }
    }

    public function loadOrSave($data, $newEntity = null)
    {
        $entity = $this->findOne($data);
        if ($entity) {
            return $entity;
        }

        if (empty($newEntity)) {
            $newEntity = $this->create($data);
        } elseif (is_callable($newEntity)) {
            $newEntity = call_user_func($newEntity);
        }
        if (!$newEntity instanceof EntityInterface) {
            throw new \LogicException("please implements EntityInterface entity (second param)");
        }
        $result = $this->save($newEntity);
        if ($result) {
            return $newEntity;
        }
    }

    public function delete(EntityInterface $entity)
    {
        return $this->deleteById($entity->getId());
    }

    public function deleteBy(array $criteria = [], $table = null)
    {
        $this->setChanged();
        if (is_null($table)) {
            $table = $this->getTable();
        }
        return $this->deleteRaw($criteria, $table);
    }

    /**
     * @param array $criteria
     * @param null $entityClass
     * @param null $limit
     * @param null $offset
     * @param null $orderBy
     * @return Collection
     * @throws \Exception
     */
    public function find(array $criteria = [], $entityClass = null, $limit = null, $offset = null, $orderBy = null)
    {
        if (is_null($entityClass)) {
            $entityClass = $this->getEntityClass();
        }
        $table = $this->getTable($entityClass);
        $idField = $this->getIdField($table);
        $data = $this->findRaw($criteria, $table, $limit, $offset, $orderBy);
        $items = new Collection();
        $idMap = $this->getIdMap($entityClass);
        foreach($data as $row) {
            $idValue = $row[$idField];
            if ($idMap->has($idValue)) {
                $item = $idMap->get($idValue);
            } else {
                $item = $this->create($row, $entityClass);
                $idMap->set($idValue, $item);
            }
            $items[] = $item;
        }
        return $items;
    }

    public function findOne(array $criteria = [], $entityClass = null)
    {
        $items = $this->find($criteria, $entityClass);
        return $items->first();
    }

    public function findReferredIds()
    {
        $refIds = $this->getReferredIds();
        $items = $this->findByIds($refIds);
        $this->clearReferredIds();
        return $items;
    }

    /**
     * @param $id
     * @param null $entityClass
     * @return EntityInterface
     * @throws \Exception
     */
    public function findById($id, $entityClass = null)
    {
        $table = $this->getTable($entityClass);
        $idName = $this->getIdField($table);
        $idMap = $this->getIdMap($entityClass);
        if ($idMap->has($id)) {
            return $idMap->get($id);
        }
        $refIds = $this->getReferredIds();
        if (in_array($id, $refIds)) {
            $this->findReferredIds();
            if ($idMap->has($id)) {
                return $idMap->get($id);
            }
        }
        $item = $this->findOne([$idName=>$id], $entityClass);
        return $item;
    }

    public function findByIds(array $ids, $entityClass = null)
    {
        $table = $this->getTable($entityClass);
        $idName = $this->getIdField($table);
        $idMap = $this->getIdMap($entityClass);
        $needFindIds = $idMap->getDiff($ids);
        if (!empty($needFindIds)) {
            $this->find([$idName=>$needFindIds], $entityClass);
        }
        $items = $idMap->getIds($ids);

        return new Collection($items);
    }

    public function getLoadFieldsList()
    {
        $fields = $this->getFieldsList();
        $externalFields = $this->getExternalFieldsList();
        if (!empty($externalFields)) {
            $fields = array_values(array_unique(array_merge($fields, $externalFields)));
        }
        $fields = array_flip($fields);
        return $fields;
    }

    public function load(EntityInterface $entity, array $data)
    {
        $fields = $this->getLoadFieldsList();
        $data = array_intersect_key($data, $fields);
        foreach($data as $field=>$value) {
            $this->setField($entity, $field, $value);
        }
        return $entity;
    }

    public function loadUntrusted(EntityInterface $entity, array $data)
    {
        $entity->setUntrusted();
        return $this->load($entity, $data);
    }

    public function loadFromElastic(EntityInterface $entity, array $data)
    {
        return $this->loadUntrusted($entity, $data);
    }

    public function reserve(EntityInterface $entity)
    {
        $fields = $this->getFieldsList();
        $data = [];
        foreach ($fields as $field) {
            $value = $this->getField($entity, $field);
            if ($value instanceof EntityInterface) {
                $value = $value->getId();
            }
            if ($value instanceof \DateTime) {
                $value = $value->format("Y-m-d H:i:s");
            }
            if (is_bool($value)) {
                $value = $value ? 't' : 'f';
            }
            $data[$field] = $value;
        }
        return ["fields" => $data];
    }

    public function count(array $criteria = [], $entityClass = null)
    {
        $adapter = $this->getAdapter();
        $table = $this->getTable($entityClass);
        return $adapter->count($table, $criteria);
    }

    public function serializeFields($data, $fieldNames)
    {
        $fieldNames = (array) $fieldNames;
        foreach($fieldNames as $fieldName) {
            if (isset($data["fields"][$fieldName]) && is_array($data["fields"][$fieldName])) {
                 if (!empty($data["fields"][$fieldName])) {
                     $data["fields"][$fieldName] = serialize($data["fields"][$fieldName]);
                 } else {
                     $data["fields"][$fieldName] = null;
                 }
            }
        }
        return $data;
    }

    public function unserializeFields($data, $fieldNames)
    {
        $fieldNames = (array) $fieldNames;
        foreach ($fieldNames as $fieldName) {
            if (isset($data[$fieldName])) {
                $value = @unserialize($data[$fieldName]);
                $data[$fieldName] = $value ?: [];
            }
        }
        return $data;
    }

    public function filterData($data)
    {
        $fields = $this->getFieldsList();
        $externalFields = $this->getExternalFieldsList();
        if (!empty($externalFields)) {
            $fields = array_values(array_unique(array_merge($fields, $externalFields)));
        }
        $fields = array_flip($fields);
        $data = array_intersect_key($data, $fields);
        $criteria = [];
        foreach($data as $field=>$value) {
            $criteria[$field] = $value;
        }
        return $criteria;
    }

    public function filterCriteria($criteria)
    {
        if (empty($criteria)) {
            return $criteria;
        }
        $fieldsList = array_flip($this->getFieldsList());
        $criteria = array_filter($criteria, function ($key) use ($fieldsList) {
            return array_key_exists($key, $fieldsList);
        },
            ARRAY_FILTER_USE_KEY);
        return $criteria;
    }

    public function begin()
    {
        return $this->getAdapter()->begin();
    }

    public function commit()
    {
        return $this->getAdapter()->commit();
    }

    public function rollback()
    {
        return $this->getAdapter()->rollBack();
    }

    public function getChangedFieldName()
    {
        $fields = $this->getFieldsList();
        if (in_array("changed", $fields)) {
            return "changed";
        }
        return null;
    }

    public function getLastChangedDate(array $criteria = [])
    {
        $table = $this->getTable();
        $criteria = $this->filterCriteria($criteria);
        if (null === $this->lastChanged && $changedField = $this->getChangedFieldName()) {
            $this->lastChanged = $this->getAdapter()->max($table, $changedField, $criteria);
            if (null !== $this->lastChanged) {
                $this->lastChanged = new \DateTime($this->lastChanged);
            }
        }
        return $this->lastChanged;
    }
}

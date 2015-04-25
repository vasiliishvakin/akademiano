<?php

namespace DeltaDb;


use DeltaCore\Parts\MagicSetGetManagers;
use DeltaCore\Prototype\MagicMethodInterface;
use DeltaDb\Adapter\AdapterInterface;
use DeltaUtils\ArrayUtils;
use DeltaUtils\Parts\InnerCache;
use DeltaUtils\StringUtils;
use Psr\Log\InvalidArgumentException;

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
        'tableName' => [
            'class'  => 'Entity',
            'id'     => 'id_field',
            'fields' => [
                'field_name' => [
                    'set'        => 'setMethodInEntity',
                    'get'        => 'getMethodInEntity',
                    'filters'    => [
                        'input'  => 'filterFromDbToEntity',
                        'output' => 'filterFromEntityDb',
                    ],
                    'validators' => [

                    ]
                ]
            ]
        ]
    ];

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

    public function setTable($table, array $tableData = [], $merge = true)
    {
        $currentMeta = $this->getMetaInfo();
        if (isset($currentMeta[$table]) && $merge) {
            $metaAdd =  [$table => $tableData];
            $this->metaInfo = ArrayUtils::mergeRecursive($currentMeta, $metaAdd);
        } else {
            $this->metaInfo[$table] = $tableData;
        }
    }

    public function renameTable($oldName, $newName)
    {
        $currentMeta = $this->getMetaInfo();
        if (!isset($currentMeta[$oldName])) {
            throw new InvalidArgumentException("Table {$oldName} not exist in " . __CLASS__);
        }
        $tableInfo = $currentMeta[$oldName];
        unset($this->metaInfo[$oldName]);
        $this->setTable($newName, $tableInfo, false);
    }

    public function getEntityClass($table = null)
    {
        $meta = $this->getMetaInfo();
        if (is_null($table)) {
            $table = $this->getTableName();
        }
        return $meta[$table]['class'];
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
     * @return array
     * @todo Merge with default: don't get form $metaInfo
     */
    public function getMetaInfo()
    {
        return $this->metaInfo;
    }

    public function getTableName($entity = null)
    {
        $entityClass = (is_null($entity)) ? null : is_object($entity) ? '\\' .get_class($entity) : $entity;
        $cacheId = "tableName|{$entityClass}|";
        if ($tableName = $this->getInnerCache($cacheId)) {
            return $tableName;
        }
        $meta = $this->getMetaInfo();
        $tables = array_keys($meta);
        $tableName = null;
        if (is_null($entityClass)) {
            $tableName = reset($tables);
        } else {
            foreach ($tables as $table) {
                if ($meta[$table]['class'] === $entityClass) {
                    $tableName = $table;
                    break;
                }
            }
        }
        if (empty ($tableName)) {
            throw new \Exception("Meta info for entity $entityClass not defined");
        }
        $this->setInnerCache($cacheId, $tableName);
        return $tableName;
    }

    public function getIdField($table)
    {
        $meta = $this->getMetaInfo();
        return $meta[$table]['id'];
    }

    public function getExternalFieldsList()
    {
        $cacheId = "externalFieldList";
        if ($fields = $this->getInnerCache($cacheId)) {
            return $fields;
        }
        $meta = $this->getMetaInfo();
        $fieldsData = isset($meta['externalFields']) ? $meta['externalFields'] : [];
        if (ArrayUtils::isAssoc($fieldsData)) {
            $fields = array_keys($fieldsData);
        } else {
            $fields = array_values($fieldsData);
        }
        $this->setInnerCache($cacheId, $fields);
        return $fields;
    }

    public function getFieldsList($table = null)
    {
        if (null === $table) {
            $table = $this->getTableName();
        }
        $cacheId = "fieldList|{$table}|";
        if ($fields = $this->getInnerCache($cacheId)) {
            return $fields;
        }
        $meta = $this->getMetaInfo();
        $fieldsData = $meta[$table]['fields'];
        if (ArrayUtils::isAssoc($fieldsData)) {
            $fields = array_keys($fieldsData);
        } else {
            $fields = array_values($fieldsData);
        }
        $this->setInnerCache($cacheId, $fields);
        return $fields;
    }

    public function getFieldMeta($table, $field)
    {
        $cacheId = "fieldMeta|$field";
        if ($this->hasInnerCache($cacheId)) {
            return $this->getInnerCache($cacheId);
        }
        $meta = $this->getMetaInfo();
        $fieldMeta = ArrayUtils::getByPath($meta, [$table, 'fields', $field]);
        if (!$fieldMeta) {
            $fieldMeta = ArrayUtils::getByPath($meta, ["externalFields", $field]);
        }
        $this->setInnerCache($cacheId, $fieldMeta);
        return $fieldMeta;
    }

    public function getFieldMethod($table, $field, $method)
    {
        $fieldMeta = $this->getFieldMeta($table, $field);
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

    public function getFieldFilter($table, $field, $filter)
    {
        $fieldMeta = $this->getFieldMeta($table, $field);
        if (!$fieldMeta) {
            return null;
        }
        return ArrayUtils::getByPath($fieldMeta, ["filters", $filter]);
    }

    public function getFieldValidators($table, $field)
    {
        $meta = $this->getMetaInfo();
        if (!isset($meta[$table]['fields'][$field]['validators'])) {
            return [];
        }
        $validators = $meta[$table]['fields'][$field]['validators'];
        return $validators;
    }

    public function validateField($entity, $field, $value)
    {
        $table = $this->getTableName($entity);
        $validators = $this->getFieldValidators($table, $field);
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
        $table = $this->getTableName($entity);
        $setMethod = $this->getFieldMethod($table, $field, self::METHOD_SET);
        $inputFilter = $this->getFieldFilter($table, $field, self::FILTER_IN);
        if ($this->checkMethodCallable($entity, $inputFilter)) {
            $value = $entity->{$inputFilter}($value);
        }

        if ($this->checkMethodCallable($entity, $setMethod)) {
            return $entity->{$setMethod}($value);
        }
        return false;
    }

    public function getField(EntityInterface $entity, $field)
    {
        $table = $this->getTableName($entity);
        $getMethod = $this->getFieldMethod($table, $field, self::METHOD_GET);
        $outputFilter = $this->getFieldFilter($table, $field, self::FILTER_OUT);
        if (!$this->checkMethodCallable($entity, $getMethod)) {
            return null;
        }
        $value =  $entity->{$getMethod}();
        if ($this->checkMethodCallable($entity, $outputFilter)) {
            $value = $entity->{$outputFilter}($value);
        }
        return $value;
    }

    public function findRaw(array $criteria = [], $table = null, $limit = null, $offset = null, $orderBy = null)
    {
        $adapter = $this->getAdapter();
        if (is_null($table)) {
            $table = $this->getTableName();
        }
        $data = $adapter->selectBy($table, $criteria, $limit, $offset, $orderBy);
        return $data;
    }

    public function saveRaw(array $fields, $table = null, $rawFields = null)
    {
        if (is_null($table)) {
            $table = $this->getTableName();
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
            $table = $this->getTableName();
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
            $table = $this->getTableName();
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
            $table = $this->getTableName();
        }
        return $adapter->delete($table, $criteria);
    }

    public function deleteById($id, $table = null)
    {
        if (is_null($table)) {
            $table = $this->getTableName();
        }
        $idField = $this->getIdField($table);
        return $this->deleteBy([$idField => $id], $table);
    }

    public function create(array $data = null, $entityClass = null)
    {
        if (is_null($entityClass)) {
            $entityClass = $this->getEntityClass();
        }
        /** @var EntityInterface $entity */
        $entity = new $entityClass;
        if (!is_null($data)) {
            $this->load($entity, $data);
        }
        return $entity;
    }

    public function save(EntityInterface $entity)
    {
        $data = $this->reserve($entity);
        $fields = isset($data["fields"]) ? $data["fields"] : $data;
        $rawFields = isset($data["rawFields"]) ? $data["rawFields"] : null;
        $table = $this->getTableName($entity);
        $idField = $this->getIdField($table);
        if (isset($fields[$idField]) && !empty($fields[$idField])) {
            return $this->updateRaw($fields, $table, $rawFields);
        } else {
            $result = $this->insertRaw($fields, $table, $rawFields);
            if (!$result) {
                return false;
            }
            $this->setField($entity, $idField, $result);
            return true;
        }
    }

    public function delete(EntityInterface $entity)
    {
        return $this->deleteById($entity->getId());
    }

    public function deleteBy(array $criteria = [], $table = null)
    {
        if (is_null($table)) {
            $table = $this->getTableName();
        }
        return $this->deleteRaw($criteria, $table);
    }

    /**
     * @param array $criteria
     * @param null $entityClass
     * @param null $limit
     * @param null $offset
     * @param null $orderBy
     * @return EntityInterface[]
     * @throws \Exception
     */
    public function find(array $criteria = [], $entityClass = null, $limit = null, $offset = null, $orderBy = null)
    {
        if (is_null($entityClass)) {
            $entityClass = $this->getEntityClass();
        }
        $table = $this->getTableName($entityClass);
        $idField = $this->getIdField($table);
        $data = $this->findRaw($criteria, $table, $limit, $offset, $orderBy);
        $items = [];
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
        if (empty($items)) {
            return null;
        }
        return reset($items);
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
        $table = $this->getTableName($entityClass);
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
        $table = $this->getTableName($entityClass);
        $idName = $this->getIdField($table);
        $idMap = $this->getIdMap($entityClass);
        $needFindIds = $idMap->getDiff($ids);
        if (!empty($needFindIds)) {
            $this->find([$idName=>$needFindIds], $entityClass);
        }
        $items = $idMap->getIds($ids);
        return $items;
    }

    public function load(EntityInterface $entity, array $data)
    {
        $table = $this->getTableName($entity);
        $fields = $this->getFieldsList($table);
        $externalFields = $this->getExternalFieldsList();
        if (!empty($externalFields)) {
            $fields = array_values(array_unique(array_merge($fields, $externalFields)));
        }
        $fields = array_flip($fields);
        $data = array_intersect_key($data, $fields);
        foreach($data as $field=>$value) {
            $this->setField($entity, $field, $value);
        }
    }

    public function reserve(EntityInterface $entity)
    {
        $table = $this->getTableName();
        $fields = $this->getFieldsList($table);
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
        $table = $this->getTableName($entityClass);
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
        $table = $this->getTableName();
        $fields = $this->getFieldsList($table);
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
}

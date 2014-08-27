<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Articles\Model;


use Attach\Model\FileManager;
use DeltaDb\Adapter\PgsqlAdapter;
use DeltaDb\EntityInterface;
use DeltaDb\Repository;
use DeltaUtils\ArrayUtils;
use DictDir\Model\UniDirectoryManager;

class ArticlesManager extends Repository
{
    const CATEGORIES_MIDDLE_TABLE = "article_categories_matrix";

    /**
     * @var UniDirectoryManager
     */
    protected $categoryManager;

    /** @var  FileManager */
    protected $fileManager;

    protected $metaInfo = [
        'articles' => [
            'class'  => '\\Articles\\Model\\Article',
            'id'     => 'id',
            'fields' => [
                "id",
                "title",
                "description",
                "text",
                "created",
                "changed",
            ]
        ],
        'externalFields' => [
            "categories"
        ]
    ];

    /**
     * @param \DictDir\Model\UniDirectoryManager $categoryManager
     */
    public function setCategoryManager($categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    /**
     * @return \DictDir\Model\UniDirectoryManager
     */
    public function getCategoryManager()
    {
        return $this->categoryManager;
    }

    /**
     * @param \Attach\Model\FileManager $fileManager
     */
    public function setFileManager($fileManager)
    {
        $this->fileManager = $fileManager;
    }

    /**
     * @return \Attach\Model\FileManager
     */
    public function getFileManager()
    {
        return $this->fileManager;
    }

    //TODO add referenced table criteria
    public function prepareCriteria(array $criteria = [])
    {
        if (empty($criteria)) {
            return [];
        }
        $nc = [];
        $table = $this->getTableName();
        $fields = array_flip($this->getFieldsList($table));
        foreach ($criteria as $field => $value) {
            if (isset($fields[$field])) {
                $nc["mt.{$field}"] = $value;
            } else {
                switch($field) {
                    case "category" :
                        $nc["mct.category"] = $value;
                        break;
                }
            }
        }
        return $nc;
    }

    public function findRaw(array $criteria = [], $table = null, $limit = null, $offset = null, $orderBy = null)
    {
        /** @var PgsqlAdapter $adapter */
        $adapter = $this->getAdapter();
        if (is_null($table)) {
            $table = $this->getTableName();
        }
        $midCatTable = self::CATEGORIES_MIDDLE_TABLE;
        $whereJoin = "";
        $criteria = $this->prepareCriteria($criteria);
        $where = $adapter->getWhere($criteria);

        $query = "select mt.id, mt.title, mt.description, mt.text, mt.created, mt.changed,
            string_agg(mct.category::text, ',') categories
            from {$table} mt
            {$whereJoin}
            left join {$midCatTable} mct on mct.article=mt.id
            {$where}
            group by mt.id";
        $orderStr = $adapter->getOrderBy($orderBy);
        $query .= $orderStr;
        $limitSql = $adapter->getLimit($limit, $offset);
        $query .= $limitSql;
        $whereParams = $adapter->getWhereParams($criteria);
        array_unshift($whereParams, $query);
        return call_user_func_array([$adapter, 'select'], $whereParams);
    }

    public function count(array $criteria = [], $entityClass = null)
    {
        if (!isset($criteria["category"])) {
            return parent::count($criteria, $entityClass);
        }
        $adapter = $this->getAdapter();
        $table = $this->getTableName();
        $midCatTable = self::CATEGORIES_MIDDLE_TABLE;
        $whereJoin = "";
        if (isset($criteria["category"])) {
            $whereJoin = "join {$midCatTable} mct on mct.article=mt.id";
        }
        $criteria = $this->prepareCriteria($criteria);
        $where = $adapter->getWhere($criteria);
        $query = "select count(*)
            from {$table} mt
            {$whereJoin}
            {$where}";
        $whereParams = $adapter->getWhereParams($criteria);
        array_unshift($whereParams, $query);
        $count = call_user_func_array([$adapter, 'selectCell'], $whereParams);
        return $count;
    }

    public function create(array $data = null, $entityClass = null)
    {
        $entity = parent::create($data, $entityClass);
        $cm = $this->getCategoryManager();
        $fm = $this->getFileManager();
        $entity->setCategoryManager($cm);
        $entity->setFileManager($fm);
        return $entity;
    }

    public function load(EntityInterface $entity, array $data)
    {
        if (isset($data["categories"])) {
            $data["categories"] = is_array($data["categories"]) ? $data["categories"] : explode(',', $data["categories"]);
            foreach ($data["categories"] as $key => $category) {
                $data["categories"][$key] = (integer)$category;
            }
        } else {
            $data["categories"] = [];
        }
        if (!empty($data["categories"])) {
            $cm = $this->getCategoryManager();
            $cm->addReferredIds($data["categories"]);
        }
        parent::load($entity, $data);
        return $entity;
    }

    public function getCategories()
    {
        $cm = $this->getCategoryManager();
        return $cm->find();
    }

    public function clearCategories($articleId)
    {
        $adapter = $this->getAdapter();
        $midCatTable = self::CATEGORIES_MIDDLE_TABLE;
        $criteria = ["article" => $articleId];
        return $adapter->delete($midCatTable, $criteria);
    }

    public function saveCategories($articleId, array $categories)
    {
        $adapter = $this->getAdapter();
        $midCatTable = self::CATEGORIES_MIDDLE_TABLE;
        foreach ($categories as $category) {
            $adapter->insert($midCatTable, ["article" => $articleId, "category" => $category]);
        }
    }

    public function save(EntityInterface $entity)
    {
        $categories = $entity->getCategoriesIds();
        parent::save($entity);
        $id = $entity->getId();
        $this->clearCategories($id);
        $this->saveCategories($id, $categories);
    }

    public function deleteById($id, $table = null)
    {
        $this->clearCategories($id);
        return parent::deleteById($id, $table);
    }

    public function delete(EntityInterface $entity)
    {
        $id = $entity->getId();
        $this->clearCategories($id);
        return parent::delete($entity);
    }

    public function getDates()
    {
        $table = $this->getTableName();
        $sql = "select distinct to_char(created, 'YYYY-MM-DD') from {$table}";
        return $this->getAdapter()->selectCol($sql);
    }

    public function getMonths()
    {
        $table = $this->getTableName();
        $sql = "select distinct to_char(created, 'YYYY-MM') from {$table}";
        $months = $this->getAdapter()->selectCol($sql);
        $months = array_map(function($value) {return new \DateTime($value);}, ArrayUtils::filterNulls($months));
        return $months;
    }


} 
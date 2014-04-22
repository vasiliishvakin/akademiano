<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace News\Model;


use Attach\Model\FileManager;
use DeltaDb\Adapter\PgsqlAdapter;
use DeltaDb\EntityInterface;
use DeltaDb\Repository;
use DictDir\Model\UniDirectoryManager;

class NewsManager extends Repository
{
    const CATEGORIES_MIDDLE_TABLE = "news_categories";

    /**
     * @var UniDirectoryManager
     */
    protected $categoryManager;

    /** @var  FileManager */
    protected $fileManager;

    protected $metaInfo = [
        'news' => [
            'class'  => '\\News\\Model\\Article',
            'id'     => 'id',
            'fields' => [
                "id",
                "title",
                "short_description",
                "description",
                "text",
                "created",
                "changed",
            ]
        ],
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
                        $nc["nc.category"] = $value;
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
        if (isset($criteria["category"])) {
            $whereJoin = "join {$midCatTable} nc on nc.news=mt.id";
        }
        $criteria = $this->prepareCriteria($criteria);
        $where = $adapter->getWhere($criteria);

        $query = "select mt.id, mt.title, mt.description, mt.text, mt.created, mt.changed,
            string_agg(mct.category::text, ',') categories
            from {$table} mt
            {$whereJoin}
            left join {$midCatTable} mct on mct.news=mt.id
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
            $whereJoin = "join {$midCatTable} nc on nc.news=mt.id";
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
        $cm = $this->getCategoryManager();
        $cm->addReferredIds($data["categories"]);
        parent::load($entity, $data);
        $entity->setCategories($data["categories"]);
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
        $criteria = ["news" => $articleId];
        return $adapter->delete($midCatTable, $criteria);
    }

    public function saveCategories($articleId, array $categories)
    {
        $adapter = $this->getAdapter();
        $midCatTable = self::CATEGORIES_MIDDLE_TABLE;
        foreach ($categories as $category) {
            $adapter->insert($midCatTable, ["news" => $articleId, "category" => $category]);
        }
    }

    public function reserve(EntityInterface $entity)
    {
        $fields = parent::reserve($entity); // TODO: Change the autogenerated stub
        if (isset($fields["created"]) && $fields["created"] instanceof \DateTime) {
            $fields["created"] = $fields["created"]->format("Y-m-d H:i:s");
        }
        if (isset($fields["changed"]) && $fields["changed"] instanceof \DateTime) {
            $fields["changed"] = $fields["changed"]->format("Y-m-d H:i:s");
        }
        return $fields;
    }


    public function save(EntityInterface $entity)
    {
        $created = $entity->getCreated();
        if (is_null($created)) {
            $entity->setCreated(new \DateTime());
        }
        $entity->setChanged(new \DateTime());

        $categories = $entity->getCategoriesIds();
        parent::save($entity);
        $id = $entity->getId();
        $this->clearCategories($id);
        $this->saveCategories($id, $categories);
    }

    public function deleteById($id, $table = null)
    {
        $this->clearCategories($id);
        return parent::deleteById($id, $table); // TODO: Change the autogenerated stub
    }

    public function delete(EntityInterface $entity)
    {
        $id = $entity->getId();
        $this->clearCategories($id);
        return parent::delete($entity); // TODO: Change the autogenerated stub
    }


} 
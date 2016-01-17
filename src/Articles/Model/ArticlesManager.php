<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Articles\Model;


use DeltaCore\Parts\MagicSetGetManagers;
use DeltaDb\Adapter\PgsqlAdapter;
use DeltaDb\EntityInterface;
use DeltaDb\Repository;
use DeltaUtils\ArrayUtils;
use DeltaUtils\FileSystem;
use HttpWarp\File\FlowFile;

/**
 * Class ArticlesManager
 * @package Articles
 * @method  setCategoryManager(\DictDir\Model\UniDirectoryManager $categoryManager)
 * @method  \DictDir\Model\UniDirectoryManager getCategoryManager()
 * @method  setFileManager(\Attach\Model\FileManager $fileManager)
 * @method \Attach\Model\FileManager getFileManager()
 */
class ArticlesManager extends Repository
{
    const CATEGORIES_MIDDLE_TABLE = "article_categories_matrix";

    use MagicSetGetManagers;

    protected $metaInfo = [
        'fields' => [
            "id",
            "title",
            "description",
            "text",
            "created",
            "changed",
        ],
        'externalFields' => [
            "categories"
        ]
    ];

    //TODO add referenced table criteria
    public function prepareCriteria(array $criteria = [])
    {
        if (empty($criteria)) {
            return [];
        }
        $nc = [];
        $table = $this->getTable();
        $fields = array_flip($this->getFieldsList($table));
        foreach ($criteria as $field => $value) {
            if (isset($fields[$field])) {
                $nc["mt.{$field}"] = $value;
            } else {
                switch ($field) {
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
            $table = $this->getTable();
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
        $table = $this->getTable();
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
        $cm = $this->getCategoryManager();
        if (isset($data["categories"])) {
            $data["categories"] = is_array($data["categories"]) ? $data["categories"] : explode(',', $data["categories"]);
            foreach ($data["categories"] as $key => $category) {
                if (is_numeric($category)) {
                    $data["categories"][$key] = (integer)$category;
                } else {
                    $query = ['name' => $category];
                    $category = $cm->findOne($query);
                    if (!$category) {
                        $category = $cm->create($query);
                        $cm->save($category);
                    }
                    $data["categories"][$key] = $category->getId();
                }
            }
        } else {
            $data["categories"] = [];
        }
        if (!empty($data["categories"])) {
            $cm->addReferredIds($data["categories"]);
        }
        if (isset($data['images'])) {
            $fm = $this->getFileManager();
            foreach ($data['images'] as $image) {
                if (isset($image['id'])) {
                    if (isset($image['removed']) && $image['removed'] === true) {
                        $fm->deleteById($image['id']);
                    } else {
                        /** @var \Attach\Model\File $row */
                        $row = $fm->findById($image['id']);
                        if (isset($image['name'])) {
                            $row->setName($image['name']);
                        }
                        if (isset($image['description'])) {
                            $row->setDescription($image['description']);
                        }
                        $fm->save($row);
                    }
                }
            }
        }
        if (isset($data['flowImages'])) {
            $fm = $this->getFileManager();
            foreach ($data['flowImages'] as $image) {
                if (isset($image['uniqueIdentifier'])) {
                    $type = FileSystem::FST_IMAGE;
//                    $maxFileSize = $this->getConfig(["Articles", "Attach", "Size"], 500*1024);
                    $path = ROOT_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $image['uniqueIdentifier'];
                    $file = new FlowFile($image['uniqueIdentifier'], $path);
                    if (!$file->checkType($type)) {
                        continue;
                    }
//                    if ($file->getSize() > $maxSize) {
//                        continue;
//                    }
                    $title = '';
                    if (isset($image['name'])) {
                        $title = $image['name'];
                    }
                    $description = '';
                    if (isset($image['description'])) {
                        $description = $image['description'];
                    }
                    $fm->saveFileForObject($entity, $file, $title, $description);
                }
            }
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
        $this->getFileManager()->deleteFilesForObjects($id, $this->getEntityClass());
        return parent::deleteById($id, $table);
    }

    public function delete(EntityInterface $entity)
    {
        $id = $entity->getId();
        $this->clearCategories($id);
        $this->getFileManager()->deleteFilesForObjects($entity);
        return parent::delete($entity);
    }

    public function getDates()
    {
        $table = $this->getTable();
        $sql = "select distinct to_char(created, 'YYYY-MM-DD') from {$table}";
        return $this->getAdapter()->selectCol($sql);
    }

    public function getMonths()
    {
        $table = $this->getTable();
        $sql = "select distinct to_char(created, 'YYYY-MM') from {$table}";
        $months = $this->getAdapter()->selectCol($sql);
        $months = array_map(function ($value) {
            return new \DateTime($value);
        }, ArrayUtils::filterNulls($months));
        return $months;
    }


} 